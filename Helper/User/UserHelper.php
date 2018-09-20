<?php

namespace Troopers\MangopayBundle\Helper\User;

use Doctrine\ORM\EntityManager;
use MangoPay\UserNatural;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Troopers\MangopayBundle\Entity\LegalUserInterface;
use Troopers\MangopayBundle\Entity\NaturalUserInterface;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Event\UserEvent;
use Troopers\MangopayBundle\TroopersMangopayEvents;
use Troopers\MangopayBundle\Helper\MangopayHelper;
use Troopers\MangopayBundle\Entity\BankInformationInterface;

class UserHelper
{
    private $mangopayHelper;
    private $naturalUserHelper;
    private $legalUserHelper;

    /**
     * UserHelper constructor.
     * @param NaturalUserHelper $naturalUserHelper
     * @param LegalUserHelper $legalUserHelper
     * @param MangopayHelper $mangopayHelper
     */
    public function __construct(NaturalUserHelper $naturalUserHelper, LegalUserHelper $legalUserHelper, MangopayHelper $mangopayHelper)
    {
        $this->naturalUserHelper = $naturalUserHelper;
        $this->legalUserHelper = $legalUserHelper;
        $this->mangopayHelper = $mangopayHelper;
    }

    /**
     * @param UserInterface $user
     * @return \MangoPay\UserLegal|UserNatural
     * @throws \Exception
     */
    public function findOrCreateMangoUser(UserInterface $user)
    {
        if ($mangoUserId = $user->getMangoUserId()) {
            $mangoUser = $this->mangopayHelper->Users->get($mangoUserId);
        } else {
            $mangoUser = $this->createMangoUser($user);
        }

        return $mangoUser;
    }

    /**
     * @param UserInterface $user
     * @return \MangoPay\UserLegal|UserNatural
     * @throws \Exception
     */
    public function createMangoUser(UserInterface $user)
    {
        return $this->getUserHelper($user)->createMangoUser($user);
    }

    /**
     * @param UserInterface $user
     * @return \MangoPay\UserLegal|UserNatural
     * @throws \Exception
     */
    public function updateMangoUser(UserInterface $user)
    {
        return $this->getUserHelper($user)->updateMangoUser($user);
    }

    /**
     * @param BankInformationInterface $bankInformation
     * @return mixed
     * @throws \Exception
     */
    public function createBankAccount(BankInformationInterface $bankInformation)
    {
        return $this->getUserHelper($bankInformation->getUser())->createBankAccount($bankInformation);
    }

    public function getTransactions($userId)
    {
        return $this->mangopayHelper->Users->GetTransactions($userId);
    }

    /**
     * @param UserInterface $user
     * @return LegalUserHelper|NaturalUserHelper
     * @throws \Exception
     */
    protected function getUserHelper(UserInterface $user)
    {
        switch (true) {
            case $user instanceof NaturalUserInterface:
                return $this->naturalUserHelper;
                break;
            case $user instanceof LegalUserInterface:
                return $this->legalUserHelper;
                break;
            default:
                throw new \Exception("Unable to find a UserHelper that match given user: " . get_class($user));
        }
    }

}
