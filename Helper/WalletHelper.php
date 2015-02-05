<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\WalletEvent;
use MangoPay\Wallet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * ref: appventus_mangopay.wallet_helper
 *
 **/
class WalletHelper
{
    private $mangopayHelper;
    private $dispatcher;

    public function __construct(MangopayHelper $mangopayHelper, EventDispatcherInterface $dispatcher)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->dispatcher = $dispatcher;
    }

    public function findOrCreateWallet(UserInterface $user, $description = '')
    {

        if ($wallet = $user->getWallet()) {
            $wallet = $this->mangopayHelper->Wallets->get($wallet->getMangoId());
        // else, create a new mango user
        } else {
            $wallet = $this->createWalletForUser($user, $description);
        }

        return $wallet;
    }

    public function createWalletForUser(UserInterface $user, $description = '')
    {
        $mangoWallet = new Wallet();
        $mangoWallet->Owners = array($user->getMangoUserId());
        $mangoWallet->Currency = "EUR";
        $mangoWallet->Description = $description;

        $mangoWallet = $this->mangopayHelper->Wallets->create($mangoWallet);

        $event = new WalletEvent($wallet, $user);
        $this->dispatcher->dispatch(AppVentusMangopayEvents::NEW_WALLET, $event);

        return $wallet;
    }

}
