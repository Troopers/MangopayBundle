<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\BankInformationInterface;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\UserEvent;
use Doctrine\ORM\EntityManager;
use MangoPay\BankAccount;
use MangoPay\BankAccountDetailsIBAN;
use MangoPay\User;
use MangoPay\UserNatural;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * ref: appventus_mangopay.bank_information_helper
 *
 **/
class BankInformationHelper
{
    private $mangopayHelper;
    private $entityManager;
    private $userHelper;

    public function __construct(MangopayHelper $mangopayHelper, EntityManager $entityManager, UserHelper $userHelper)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->userHelper = $userHelper;
        $this->entityManager = $entityManager;
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
        $bankAccount->OwnerName    = $bankInformation->getUser()->getFullName();
        $bankAccount->UserId       = $mangoUser->Id;
        $bankAccount->Type         = "IBAN";
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

}
