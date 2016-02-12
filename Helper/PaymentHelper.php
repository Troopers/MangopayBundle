<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\CardPreAuthorisation;
use AppVentus\MangopayBundle\Entity\Order;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\CardRegistrationEvent;
use AppVentus\MangopayBundle\Event\PayInEvent;
use AppVentus\MangopayBundle\Event\PreAuthorisationEvent;
use AppVentus\MangopayBundle\Exception\MongopayPayInCreationException;
use MangoPay\CardPreAuthorization;
use MangoPay\CardRegistration;
use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInExecutionDetailsDirect;
use MangoPay\PayInPaymentDetailsPreAuthorized;
use MangoPay\User;
use MangoPay\Wallet;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * ref: appventus_mangopay.payment_helper
 *
 **/
class PaymentHelper
{
    protected $mangopayHelper;
    protected $router;
    protected $dispatcher;

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
    /**
     * execute a pre authorisation
     * @param CardPreAuthorisation $preAuthorisation
     * @param UserInterface        $buyer
     * @param Wallet               $wallet
     * @param integer              $feesAmount
     * @param integer              $amount           0 to 100
     *
     * @return PayIn
     */
    public function executePreAuthorisation(
        CardPreAuthorisation $preAuthorisation,
        UserInterface $buyer,
        Wallet $wallet,
        $feesAmount,
        $amount = null
    )
    {

        if (!$amount) {
            $amount = $preAuthorisation->getDebitedFunds();
        }

        $payIn = new PayIn();
        $payIn->AuthorId = $buyer->getMangoUserId();
        $payIn->CreditedWalletId = $wallet->Id;
        $payIn->PreauthorizationId = $preAuthorisation->getMangoId();
        $payIn->PaymentDetails = new PayInPaymentDetailsPreAuthorized();
        $payIn->ExecutionDetails = new PayInExecutionDetailsDirect();

        $fees = new Money();
        $fees->Currency = 'EUR';
        $fees->Amount = $feesAmount;

        $payIn->Fees = $fees;

        $debitedFunds = new Money();
        $debitedFunds->Currency = 'EUR';
        $debitedFunds->Amount = $amount;
        $payIn->DebitedFunds = $debitedFunds;

        $payIn = $this->mangopayHelper->PayIns->Create($payIn);

        if (property_exists($payIn, 'Status') && $payIn->Status != "FAILED") {
            $event = new PayInEvent($payIn);
            $this->dispatcher->dispatch(AppVentusMangopayEvents::NEW_PAY_IN, $event);

           return $payIn;
        }

        $event = new PayInEvent($payIn);
        $this->dispatcher->dispatch(AppVentusMangopayEvents::ERROR_PAY_IN, $event);

        throw new MongopayPayInCreationException($this->translator->trans(
            'mangopay.error.'. $payIn->ResultCode,
            [], 'messages'
        ));

    }

    public function cancelPreAuthForOrder(Order $order, CardPreAuthorisation $preAuth)
    {
        if ($preAuth->getPaymentStatus() == "WAITING") {

            $mangoCardPreAuthorisation = $this->mangopayHelper->CardPreAuthorizations->Get($preAuth->getMangoId());
            $mangoCardPreAuthorisation->PaymentStatus = 'CANCELED';
            $this->mangopayHelper->CardPreAuthorizations->Update($mangoCardPreAuthorisation);

            $event = new PreAuthorisationEvent($order, $mangoCardPreAuthorisation);
            $this->dispatcher->dispatch(AppVentusMangopayEvents::CANCEL_CARD_PREAUTHORISATION, $event);
        }
    }

    public function generateSuccessUrl()
    {
        return $this->router->generate('appventus_mangopaybundle_payment_success');
    }

}
