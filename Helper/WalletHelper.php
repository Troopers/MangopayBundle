<?php

namespace Troopers\MangopayBundle\Helper;

use Troopers\MangopayBundle\TroopersMangopayEvents;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Event\WalletEvent;
use Doctrine\ORM\EntityManager;
use MangoPay\Wallet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * ref: troopers_mangopay.wallet_helper.
 **/
class WalletHelper
{
    private $mangopayHelper;
    private $userHelper;
    private $dispatcher;
    private $entityManager;

    public function __construct(MangopayHelper $mangopayHelper, UserHelper $userHelper, EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->userHelper = $userHelper;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserInterface $user
     * @param string        $description
     *
     * @return Wallet
     */
    public function findOrCreateWallet(UserInterface $user, $description = 'current wallet')
    {
        if ($user->getMangoWalletId()) {
            $wallet = $this->mangopayHelper->Wallets->get($user->getMangoWalletId());
        // else, create a new mango user
        } else {
            $wallet = $this->createWalletForUser($user, $description);
        }

        return $wallet;
    }

    public function createWalletForUser(UserInterface $user, $description = 'current wallet')
    {
        $mangoUser = $this->userHelper->findOrCreateMangoUser($user);
        $mangoWallet = new Wallet();
        $mangoWallet->Owners = array($mangoUser->Id);
        $mangoWallet->Currency = 'EUR';
        $mangoWallet->Description = $description;

        $mangoWallet = $this->mangopayHelper->Wallets->create($mangoWallet);

        $event = new WalletEvent($mangoWallet, $user);
        $this->dispatcher->dispatch(TroopersMangopayEvents::NEW_WALLET, $event);

        return $mangoWallet;
    }
}
