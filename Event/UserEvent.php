<?php

namespace Troopers\MangopayBundle\Event;

use MangoPay\User;
use Symfony\Component\EventDispatcher\Event;
use Troopers\MangopayBundle\Entity\UserInterface;

class UserEvent extends Event
{
    private $user;
    private $mangoUser;

    public function __construct(UserInterface $user, User $mangoUser)
    {
        $this->user = $user;
        $this->mangoUser = $mangoUser;
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

    /**
     * Get mangoUser.
     *
     * @return string
     */
    public function getMangoUser()
    {
        return $this->mangoUser;
    }

    /**
     * Set mangoUser.
     *
     * @param string $mangoUser
     *
     * @return $this
     */
    public function setMangoUser($mangoUser)
    {
        $this->mangoUser = $mangoUser;

        return $this;
    }
}
