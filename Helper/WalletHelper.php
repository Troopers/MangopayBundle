<?php

namespace Troopers\MangopayBundle\Helper;

use Doctrine\ORM\EntityManager;
use MangoPay\Wallet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Event\WalletEvent;
use Troopers\MangopayBundle\TroopersMangopayEvents;
use Troopers\MangopayBundle\Helper\User\UserHelper;

/**
 * ref: troopers_mangopay.wallet_helper.
 **/
class WalletHelper
{
    private $mangopayHelper;
    private $userHelper;
    private $dispatcher;
    private $entityManager;

    public function __construct(MangopayHelper $mangopayHelper, UserHelper $userHelper, EventDispatcherInterface $dispatcher)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->userHelper = $userHelper;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UserInterface $user
     * @param string        $description
     *
     * @return Wallet
     */
    public function findOrCreateWallet(UserInterface $user, $description = 'current wallet')
    {
        if ($walletId = $user->getMangoWalletId()) {
            $wallet = $this->mangopayHelper->Wallets->get($walletId);
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
        $mangoWallet->Owners = [$mangoUser->Id];
        $mangoWallet->Currency = 'EUR';
        $mangoWallet->Description = $description;

        $mangoWallet = $this->mangopayHelper->Wallets->create($mangoWallet);

        $event = new WalletEvent($mangoWallet, $user);
        $this->dispatcher->dispatch(TroopersMangopayEvents::NEW_WALLET, $event);

        return $mangoWallet;
    }

    public function getTransactions($walletId)
    {
        return $this->mangopayHelper->Wallets->GetTransactions($walletId);
    }
}
