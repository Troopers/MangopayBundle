<?php

namespace Troopers\MangopayBundle\Helper;

use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInPaymentDetailsDirectDebitDirect;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Troopers\MangopayBundle\Entity\Transaction;
use Troopers\MangopayBundle\Entity\TransactionInterface;
use Troopers\MangopayBundle\Entity\UserInterface;

class PaymentDirectDebitHelper
{
    private $mangopayHelper;
    private $router;
    private $dispatcher;
    /**
     * @var MandateHelper
     */
    private $mandateHelper;

    public function __construct(MangopayHelper $mangopayHelper, MandateHelper $mandateHelper)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->mandateHelper = $mandateHelper;
    }

    public function createDirectDebitPayin(UserInterface $userDebited, UserInterface $userCredited, $amount, $fees, $statementDescriptor = null)
    {
        $mandate = $this->mandateHelper->findOrCreateMandate($delivery->getFreightForwarder()->getManagedCompany());

        $payin = new PayIn();
        $payin->AuthorId = $userDebited->getMangoUserId();
        $payin->CreditedUserId = $userCredited->getMangoUserId();
        $payin->CreditedWalletId = $userCredited->getMangoWalletId();

        $debitedFunds = new Money();
        $debitedFunds->Currency = 'EUR';
        $debitedFunds->Amount = $amount;

        $fees = new Money();
        $fees->Currency = 'EUR';
        $fees->Amount = $fees;

        $payin->DebitedFunds = $debitedFunds;
        $payin->Fees = $fees;
        $payin->MandateId = $mandate->Id;

        $payin->PaymentDetails = new PayInPaymentDetailsDirectDebitDirect();
        $payin->PaymentDetails->MandateId = $mandate->Id;
        $payin->PaymentDetails->StatementDescriptor = $statementDescriptor;

        return $this->mangopayHelper->PayIns->Create($payin);
    }

    public function getPayin($payinId)
    {
        return $this->mangopayHelper->PayIns->Get($payinId);
    }
}
