<?php

namespace Troopers\MangopayBundle\Helper;

use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInExecutionDetailsDirect;
use MangoPay\PayInPaymentDetailsDirectDebit;
use MangoPay\PayInPaymentDetailsDirectDebitDirect;
use MangoPay\PayInPaymentType;
use Troopers\MangopayBundle\Entity\UserInterface;

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
        $mandate = $this->mandateHelper->findOrCreateMandate($userDebited->getBankInformation());

        $payin = new PayIn();
        $payin->AuthorId = $userDebited->getMangoUserId();
        $payin->CreditedUserId = $userCredited->getMangoUserId();
        $payin->CreditedWalletId = $userCredited->getMangoWalletId();

        $debitedFunds = new Money();
        $debitedFunds->Currency = 'EUR';
        $debitedFunds->Amount = $amount;

        $mangoFees = new Money();
        $mangoFees->Currency = 'EUR';
        $mangoFees->Amount = $fees;

        $payin->DebitedFunds = $debitedFunds;
        $payin->Fees = $mangoFees;

        $payin->PaymentDetails = new PayInPaymentDetailsDirectDebit();
        $payin->PaymentDetails->MandateId = $mandate->Id;
        $payin->PaymentDetails->StatementDescriptor = $statementDescriptor;

        $payin->ExecutionDetails = new PayInExecutionDetailsDirect();

        return $this->mangopayHelper->PayIns->Create($payin);
    }

    public function getPayin($payinId)
    {
        return $this->mangopayHelper->PayIns->Get($payinId);
    }
}
