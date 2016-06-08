<?php

namespace AppVentus\MangopayBundle\Event;

use AppVentus\MangopayBundle\Entity\UserInterface;
use MangoPay\Wallet;
use Symfony\Component\EventDispatcher\Event;

class WalletEvent extends Event
{
    private $wallet;
    private $user;

    public function __construct(Wallet $wallet, UserInterface $user)
    {
        $this->wallet = $wallet;
        $this->user = $user;
    }

    /**
     * Get wallet.
     *
     * @return string
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * Set wallet.
     *
     * @param string $wallet
     *
     * @return $this
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Get user.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user.
     *
     * @param string $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
