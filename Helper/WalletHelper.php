<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\WalletEvent;
use Doctrine\ORM\EntityManager;
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
     * @param string $description
     * @return Wallet
     */
    public function findOrCreateWallet(UserInterface $user, $description = 'current wallet')
    {

        if ($walletId = $user->getMangoPayInfo()->getWalletId()) {
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
        $mangoWallet->Owners = array($mangoUser->Id);
        $mangoWallet->Currency = "EUR";
        $mangoWallet->Description = $description;

        $mangoWallet = $this->mangopayHelper->Wallets->create($mangoWallet);

        $event = new WalletEvent($mangoWallet, $user);
        $this->dispatcher->dispatch(AppVentusMangopayEvents::NEW_WALLET, $event);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $mangoWallet;
    }

    public function getTransactions($walletId)
    {
        return $this->mangopayHelper->Wallets->GetTransactions($walletId);
    }
}
