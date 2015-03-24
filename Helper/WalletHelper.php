<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\WalletEvent;
use MangoPay\Wallet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;

/**
 *
 * ref: appventus_mangopay.wallet_helper
 *
 **/
class WalletHelper
{
    private $mangopayHelper;
    private $dispatcher;
    private $entityManager;

    public function __construct(MangopayHelper $mangopayHelper, EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
    }

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
        $mangoWallet = new Wallet();
        $mangoWallet->Owners = array($user->getMangoUserId());
        $mangoWallet->Currency = "EUR";
        $mangoWallet->Description = $description;

        $mangoWallet = $this->mangopayHelper->Wallets->create($mangoWallet);

        $event = new WalletEvent($mangoWallet, $user);
        $this->dispatcher->dispatch(AppVentusMangopayEvents::NEW_WALLET, $event);

        return $mangoWallet;
    }

}
