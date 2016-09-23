<?php

namespace Troopers\MangopayBundle\Helper;

use Troopers\MangopayBundle\Entity\TransactionInterface;
use MangoPay\Money;
use MangoPay\PayIn;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * ref: troopers_mangopay.payment_direct_helper.
 **/
class PaymentDirectHelper
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

    public function createDirectTransaction(TransactionInterface $transaction, $executionDetails = null)
    {
        $debitedFunds = new Money();
        $debitedFunds->Currency = 'EUR';
        $debitedFunds->Amount = $transaction->getDebitedFunds();

        $fees = new Money();
        $fees->Currency = 'EUR';
        $fees->Amount = $transaction->getFees();

        $payIn = new PayIn();
        $payIn->PaymentType = 'DIRECT_DEBIT';
        $payIn->AuthorId = $transaction->getAuthorId();
        $payIn->CreditedWalletId = $transaction->getCreditedWalletId();
        $payIn->DebitedFunds = $debitedFunds;
        $payIn->Fees = $fees;

        $payIn->Nature = 'REGULAR';
        $payIn->Type = 'PAYIN';

        $payIn->PaymentDetails = new \MangoPay\PayInPaymentDetailsCard();
        $payIn->PaymentDetails->CardType = 'CB_VISA_MASTERCARD';

        //@TODO : Find a better way to send default to this function to set default
        if (!$executionDetails instanceof \MangoPay\PayInExecutionDetails) {
            $payIn->ExecutionDetails = new \MangoPay\PayInExecutionDetailsWeb();
//            $payIn->ExecutionDetails->ReturnURL = 'https://www.example.com/bank';
//            $payIn->ExecutionDetails->TemplateURL = 'https://TemplateURL.com';
            $payIn->ExecutionDetails->SecureMode = 'DEFAULT';
            $payIn->ExecutionDetails->Culture = 'fr';
        } else {
            $payIn->ExecutionDetails = $executionDetails;
        }

        $mangoPayTransaction = $this->mangopayHelper->PayIns->create($payIn);

        //TODO
//        $event = new CardRegistrationEvent($cardRegistration);
//        $this->dispatcher->dispatch(TroopersMangopayEvents::NEW_CARD_REGISTRATION, $event);

        return $mangoPayTransaction;
    }
}
