<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Helper\MangopayHelper;
use MangoPay\Money;
use MangoPay\PayOut;
use MangoPay\PayOutPaymentDetailsBankWire;

/**
 *
 * ref: appventus_mangopay.payment_out_helper
 *
 **/
class PaymentOutHelper
{
    private $mangopayHelper;

    public function __construct(MangopayHelper $mangopayHelper)
    {
        $this->mangopayHelper = $mangopayHelper;
    }

    public function buildPayOutPaymentDetailsBankWire(UserInterface $user)
    {
        $meanOfPaymentDetails = new PayOutPaymentDetailsBankWire();
        $meanOfPaymentDetails->BankAccountId = $user->getMangoPayInfo()->getBankAccountId();

        return $meanOfPaymentDetails;
    }

    public function buildMoney($amount = '0', $currency = 'EUR')
    {
        $money = new Money();
        $money->Currency = $currency;
        $money->Amount = $amount;

        return $money;
    }

    public function createPayOutForUser(UserInterface $user, $debitedFunds, $fees = '0')
    {
        $debitedFunds = $this->buildMoney($debitedFunds);
        $fees = $this->buildMoney($fees);
        $meanOfPaymentDetails = $this->buildPayOutPaymentDetailsBankWire($user);

        $payOut = new PayOut();
        $payOut->AuthorId = $user->getMangoPayInfo()->getUserNaturalId();
        $payOut->DebitedWalletId = $user->getMangoPayInfo()->getWalletId();
        $payOut->PaymentType = 'BANK_WIRE';
        $payOut->DebitedFunds = $debitedFunds;
        $payOut->MeanOfPaymentDetails = $meanOfPaymentDetails;
        $payOut->fees = $fees;

        return $this->mangopayHelper->PayOuts->Create($payOut);
    }
}
