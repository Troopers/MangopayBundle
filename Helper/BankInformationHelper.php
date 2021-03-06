<?php

namespace Troopers\MangopayBundle\Helper;

use MangoPay\BankAccount;
use MangoPay\BankAccountDetailsIBAN;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Troopers\MangopayBundle\Entity\BankInformationInterface;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Helper\User\UserHelper;

class BankInformationHelper
{
    private $mangopayHelper;
    private $userHelper;
    private $mangopaySandbox;

    public function __construct(MangopayHelper $mangopayHelper, UserHelper $userHelper, $mangopaySandbox)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->userHelper = $userHelper;
        $this->mangopaySandbox = $mangopaySandbox;
    }

    /**
     * @param BankInformationInterface $bankInformation
     * @return BankAccount
     * @throws \Exception
     */
    public function findOrCreateBankAccount(BankInformationInterface $bankInformation)
    {
        if ($mangoBankAccountId = $bankInformation->getMangoBankAccountId()) {
            $mangoBankAccount = $this->mangopayHelper->Users->GetBankAccount($bankInformation->getUser()->getMangoUserId(), $mangoBankAccountId);
        } else {
            $mangoBankAccount = $this->createBankAccount($bankInformation);
        }

        return $mangoBankAccount;
    }

    /**
     * @param BankInformationInterface $bankInformation
     * @return BankAccount
     * @throws \Exception
     */
    public function createBankAccount(BankInformationInterface $bankInformation)
    {
        /** @var UserInterface $user */
        $user = $bankInformation->getUser();
        $mangoUser = $this->userHelper->findOrCreateMangoUser($user);

        $bankAccount = new BankAccount();
        $bankAccount->OwnerName = $bankInformation->getBankInformationFullName();
        $bankAccount->UserId = $mangoUser->Id;
        $bankAccount->Type = 'IBAN';

        $address = new \MangoPay\Address();
        $userAddress = $bankInformation->getBankInformationStreetAddress();
        $city = $bankInformation->getBankInformationCity();
        $postalCode = $bankInformation->getBankInformationPostalCode();
        if (null == $userAddress || null == $city || null == $postalCode) {
            throw new NotFoundHttpException(sprintf('address, city or postalCode missing for BankInformation of User id : %s', $user->getId()));
        }
        $address->AddressLine1 = $userAddress;
        $address->AddressLine2 = $bankInformation->getBankInformationAdditionalStreetAddress();
        $address->City = $city;
        $address->Country = $bankInformation->getBankInformationCountry();
        $address->PostalCode = $postalCode;
        $bankAccount->OwnerAddress = $address;

        $bankAccountDetailsIban = new BankAccountDetailsIBAN();

        $iban = $bankInformation->getIban();
        if ($this->mangopaySandbox) {
            $iban = "FR7611808009101234567890147";
        }
        $bankAccountDetailsIban->IBAN = $iban;

        $bankAccount->Details = $bankAccountDetailsIban;

        $bankAccount = $this->mangopayHelper->Users->CreateBankAccount($mangoUser->Id, $bankAccount);

        $bankInformation->setMangoBankAccountId($bankAccount->Id);

        return $bankAccount;
    }
    
    /**
     * @param BankInformationInterface $bankInformation
     * @return BankAccount
     * @throws \Exception
     */
    public function updateBankAccount(BankInformationInterface $bankInformation)
    {
        /** @var UserInterface $user */
        $user = $bankInformation->getUser();
        $bankAccount = $this->mangopayHelper->Users->GetBankAccount($user->getMangoUserId(), $bankInformation->getMangoBankAccountId());

        $bankAccount->OwnerName = $bankInformation->getBankInformationFullName();
        $bankAccount->UserId = $user->getMangoUserId();
        $bankAccount->Type = 'IBAN';

        $address = new \MangoPay\Address();
        $userAddress = $bankInformation->getBankInformationStreetAddress();
        $city = $bankInformation->getBankInformationCity();
        $postalCode = $bankInformation->getBankInformationPostalCode();
        if (null == $userAddress || null == $city || null == $postalCode) {
            throw new NotFoundHttpException(sprintf('address, city or postalCode missing for BankInformation of User id : %s', $user->getId()));
        }
        $address->AddressLine1 = $userAddress;
        $address->AddressLine2 = $bankInformation->getBankInformationAdditionalStreetAddress();
        $address->City = $city;
        $address->Country = $bankInformation->getBankInformationCountry();
        $address->PostalCode = $postalCode;
        $bankAccount->OwnerAddress = $address;

        if ($bankInformation->getIban() !== $bankAccount->Details->IBAN) {
            $iban = $bankInformation->getIban();
            if ($this->mangopaySandbox) {
                $iban = "FR7611808009101234567890147";
            }
            $bankAccount->Details->IBAN = $iban;
        }

        $bankAccount = $this->mangopayHelper->Users->UpdateBankAccount($mangoUser->Id, $bankAccount);

        return $bankAccount;
    }
}
