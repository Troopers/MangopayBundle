<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\Order;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\CardRegistrationEvent;
use AppVentus\MangopayBundle\Event\PreAuthorisationEvent;
use MangoPay\CardPreAuthorization;
use MangoPay\CardRegistration;
use MangoPay\Money;
use MangoPay\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * ref: appventus_mangopay.payment_helper
 *
 **/
class PaymentHelper
{
    private $mangopayHelper;
    private $router;
    private $dispatcher;

    public function __construct(MangopayHelper $mangopayHelper, Router $router, EventDispatcherInterface $dispatcher)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
    }

    public function prepareCardRegistrationCallback(User $user, Order $order)
    {

        $cardRegistration = new CardRegistration();
        $cardRegistration->UserId = $user->Id;
        $cardRegistration->Currency = "EUR";
        $mangoCardRegistration = $this->mangopayHelper->CardRegistrations->create($cardRegistration);

        $event = new CardRegistrationEvent($cardRegistration);
        $this->dispatcher->dispatch(AppVentusMangopayEvents::NEW_CARD_REGISTRATION, $event);

        $cardRegistrationURL = $mangoCardRegistration->CardRegistrationURL;
        $preregistrationData = $mangoCardRegistration->PreregistrationData;
        $accessKey = $mangoCardRegistration->AccessKey;

        $redirect = $this->router->generate(
            'appventus_mangopaybundle_payment_finalize',
            array(
                'orderId' => $order->getId(),
                'cardId'  => $mangoCardRegistration->Id
            )
        );

        $successRedirect = $this->generateSuccessUrl();

        return array(
            'callback' => 'payAjaxOrRedirect("'
                . $redirect . '", "'
                . $redirect . '", "'
                . $cardRegistrationURL . '", "'
                . $preregistrationData . '", "'
                . $accessKey . '", "'
                . $successRedirect . '")',
        );
    }

    /**
     * Update card registration with token
     * @param  string           $cardId
     * @param  string           $data
     * @param  string           $errorCode
     * @return CardRegistration
     */
    public function updateCardRegistration($cardId, $data, $errorCode)
    {

        $cardRegister = $this->mangopayHelper->CardRegistrations->Get($cardId);
        $cardRegister->RegistrationData = $data ? "data=" . $data : "errorCode=" . $errorCode;

        $updatedCardRegister = $this->mangopayHelper->CardRegistrations->Update($cardRegister);

        $event = new CardRegistrationEvent($updatedCardRegister);
        $this->dispatcher->dispatch(AppVentusMangopayEvents::UPDATE_CARD_REGISTRATION, $event);

        return $updatedCardRegister;
    }

    public function createPreAuthorisation(CardRegistration $updatedCardRegister, UserInterface $user, Order $order)
    {
        $card = $this->mangopayHelper->Cards->Get($updatedCardRegister->CardId);

        $cardPreAuthorisation = new CardPreAuthorization();

        $cardPreAuthorisation->AuthorId = $user->getMangoUserId();

        $debitedFunds = new Money();
        $debitedFunds->Currency = "EUR";
        $debitedFunds->Amount = $order->getMangoPrice();
        $cardPreAuthorisation->DebitedFunds = $debitedFunds;

        $cardPreAuthorisation->SecureMode = "DEFAULT";
        $cardPreAuthorisation->SecureModeReturnURL = $this->router->generate(
            'appventus_mangopaybundle_payment_finalize_secure',
            array(
                'orderId' => $order->getId(),
            ),
            true
        );

        $cardPreAuthorisation->CardId = $card->Id;

        $preAuth = $this->mangopayHelper->CardPreAuthorizations->Create($cardPreAuthorisation);

        $event = new PreAuthorisationEvent($order, $preAuth);
        $this->dispatcher->dispatch(AppVentusMangopayEvents::NEW_CARD_PREAUTHORISATION, $event);

        return $preAuth;
    }

    protected function generateSuccessUrl()
    {
        return $this->router->generate('appventus_mangopaybundle_payment_success');
    }

}
