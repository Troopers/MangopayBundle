<?php

namespace Troopers\MangopayBundle\Helper;

use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInPaymentDetailsDirectDebitDirect;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Helper\MangopayHelper;

class PaymentDirectDebitHelper
{
    protected $mangopayHelper;
    /**
     * @var MandateHelper
     */
    protected $mandateHelper;

    public function __construct(MangopayHelper $mangopayHelper, MandateHelper $mandateHelper)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->mandateHelper = $mandateHelper;
    }

    /**
     * @param UserInterface $userDebited
     * @param UserInterface $userCredited
     * @param int $amount
     * @param int $fees
     * @param string|null $statementDescriptor
     * @return PayIn
     */
    public function createDirectDebitPayin(UserInterface $userDebited, UserInterface $userCredited, $amount, $fees, $statementDescriptor = null)
    {
        $mandate = $this->mandateHelper->findOrCreateMandate($userDebited);

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
