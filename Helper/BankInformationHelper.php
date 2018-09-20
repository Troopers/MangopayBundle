<?php

namespace Troopers\MangopayBundle\Helper;

use MangoPay\BankAccount;
use MangoPay\BankAccountDetailsIBAN;
use MangoPay\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Troopers\MangopayBundle\Entity\BankInformationInterface;
use Troopers\MangopayBundle\Entity\UserInterface;

/**
 * ref: troopers_mangopay.bank_information_helper.
 **/
class BankInformationHelper
{
    private $mangopayHelper;
    private $userHelper;

    public function __construct(MangopayHelper $mangopayHelper, UserHelper $userHelper)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->userHelper = $userHelper;
    }

    public function findOrCreateBankAccount(BankInformationInterface $bankInformation)
    {
        if ($mangoBankAccountId = $bankInformation->getMangoBankAccountId()) {
            $mangoBankAccount = $this->mangopayHelper->Users->GetBankAccount($bankInformation->getUser()->getMangoUserId(), $mangoBankAccountId);
        } else {
            $mangoBankAccount = $this->createBankAccount($bankInformation);
        }

        return $mangoBankAccount;
    }

    public function createBankAccount(BankInformationInterface $bankInformation)
    {
        $mangoUser = $this->userHelper->findOrCreateMangoUser($bankInformation->getUser());
        //Create mango bank account
        $bankAccount = new BankAccount();
        $bankAccount->OwnerName = $bankInformation->getUser()->getFullName();
        $bankAccount->UserId = $mangoUser->Id;
        $bankAccount->Type = 'IBAN';
        $bankAccount->OwnerAddress = $bankInformation->getAddress();

        $bankAccountDetailsIban = new BankAccountDetailsIBAN();
        $bankAccountDetailsIban->IBAN = $bankInformation->getIban();

        $bankAccount->Details = $bankAccountDetailsIban;

        $bankAccount = $this->mangopayHelper->Users->CreateBankAccount($bankInformation->getUser()->getMangoUserId(), $bankAccount);

        $bankInformation->setMangoBankAccountId($bankAccount->Id);

        $this->entityManager->persist($bankInformation);
        $this->entityManager->flush();

        return $bankAccount;
    }

    public function createBankAccountForUser(UserInterface $user, $iban)
    {
        $bankAccount = new \MangoPay\BankAccount();
        $bankAccount->OwnerName = $this->getUserFullName($user);
        $bankAccount->UserId = $user->getMangoUserId();
        $bankAccount->Type = 'IBAN';

        $address = new \MangoPay\Address();
        $userAddress = $user->getAddress();
        $city = $user->getCity();
        $postalCode = $user->getPostalCode();
        if (null == $userAddress || null == $city || null == $postalCode) {
            throw new NotFoundHttpException(sprintf('address, city or postalCode missing for User id : %s', $user->getId()));
        }
        $address->AddressLine1 = $userAddress;
        $address->City = $city;
        $address->Country = $user->getCountry();
        $address->PostalCode = $postalCode;
        $bankAccount->OwnerAddress = $address;

        $bankAccountDetailsIban = new \MangoPay\BankAccountDetailsIBAN();
        $bankAccountDetailsIban->IBAN = $iban;

        $bankAccount->Details = $bankAccountDetailsIban;

        return $this->mangopayHelper->Users->CreateBankAccount($user->getMangoUserId(), $bankAccount);
    }

    /**
     * Implode Users's full name with firstName and lastName.
     *
     * @param User $user
     *
     * @return string
     */
    public function getUserFullName(UserInterface $user)
    {
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();

        return $firstName.' '.$lastName;
    }
}
