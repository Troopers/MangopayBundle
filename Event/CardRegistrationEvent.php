<?php

namespace AppVentus\MangopayBundle\Event;

use MangoPay\CardRegistration;
use Symfony\Component\EventDispatcher\Event;

class CardRegistrationEvent extends Event
{
    private $cardRegistration;

    public function __construct(CardRegistration $cardRegistration)
    {
        $this->cardRegistration = $cardRegistration;
    }

    /**
     * Get cardRegistration.
     *
     * @return string
     */
    public function getCardRegistration()
    {
        return $this->cardRegistration;
    }

    /**
     * Set cardRegistration.
     *
     * @param string $cardRegistration
     *
     * @return $this
     */
    public function setCardRegistration($cardRegistration)
    {
        $this->cardRegistration = $cardRegistration;

        return $this;
    }
}
