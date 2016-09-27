<?php
namespace Troopers\MangopayBundle\Helper;

use Troopers\MangopayBundle\TroopersMangopayEvents;
use Troopers\MangopayBundle\Entity\CardPreAuthorisation;
use Troopers\MangopayBundle\Entity\Order;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Event\CardRegistrationEvent;
use Troopers\MangopayBundle\Event\PayInEvent;
use Troopers\MangopayBundle\Event\PreAuthorisationEvent;
use Troopers\MangopayBundle\Exception\MongopayPayInCreationException;
use MangoPay\CardPreAuthorization;
use MangoPay\CardRegistration;
use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInExecutionDetailsDirect;
use MangoPay\PayInPaymentDetailsBankWire;
use MangoPay\PayInPaymentDetailsPreAuthorized;
use MangoPay\User;
use MangoPay\Wallet;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * ref: troopers_mangopay.bankwire_helper
 *
 **/
class BankwireHelper
{
    protected $mangopayHelper;

    public function __construct(MangopayHelper $mangopayHelper)
    {
        $this->mangopayHelper = $mangopayHelper;
    }

    /**
     * Create a bankWire as discribed here: https://docs.mangopay.com/endpoints/v2/payins#e288_the-direct-debit-web-payin-object
     * @param Wallet $wallet
     * @param        $authorId
     * @param        $creditedUserId
     * @param        $amount
     * @param        $feesAmount
     *
     * @return PayIn
     */
    public function bankwireToWallet(Wallet $wallet, $authorId, $creditedUserId, $amount, $feesAmount)
    {
        $debitedFunds = new Money();
        $debitedFunds->Amount = $amount * 100;
        $debitedFunds->Currency = 'EUR';
        $fees = new Money();
        $fees->Amount = $feesAmount;
        $fees->Currency = 'EUR';
        $payin = new PayIn();
        $payin->CreditedWalletId = $wallet->Id;
        $payin->ExecutionType = 'Direct';
        $executionDetails = new PayInExecutionDetailsDirect();
        $payin->ExecutionDetails = $executionDetails;
        $paymentDetails = new PayInPaymentDetailsBankWire();
        $paymentDetails->DeclaredDebitedFunds = $debitedFunds;
        $paymentDetails->DeclaredFees = $fees;
        $payin->PaymentDetails = $paymentDetails;
        $payin->AuthorId = $authorId;
        $payin->CreditedUserId = $creditedUserId;

        $bankWire = $this->mangopayHelper->PayIns->Create($payin);

        return $bankWire;
    }

}
