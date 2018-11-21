<?php

namespace Troopers\MangopayBundle\Helper;

use MangoPay\KycDocument;
use MangoPay\KycPage;
use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInExecutionDetailsDirect;
use MangoPay\PayInPaymentDetailsBankWire;
use MangoPay\Wallet;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * ref: troopers_mangopay.kyc_helper.
 **/
class  KYCHelper
{
    protected $mangopayHelper;

    public function __construct(MangopayHelper $mangopayHelper)
    {
        $this->mangopayHelper = $mangopayHelper;
    }

    /**
     * Create a bankWire as discribed here: https://docs.mangopay.com/endpoints/v2/payins#e288_the-direct-debit-web-payin-object.
     *
     * @param Wallet $wallet
     * @param        $authorId
     * @param        $creditedUserId
     * @param        $amount
     * @param        $feesAmount
     *
     * @return KycDocument
     */
    public function createDocument(File $file)
    {
        $page = new KycPage();

        if (false === $file = @file_get_contents($file->getPathname(), FILE_BINARY)) {
            throw new TransformationFailedException(sprintf('Unable to read the "%s" file', $value->getPathname()));
        }

        $page->File = base64_encode($file);

        $this->mangopayHelper->KycDocuments->CreateKycDocumentConsult();
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
