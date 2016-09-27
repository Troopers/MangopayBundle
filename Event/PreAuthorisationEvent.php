<?php

namespace Troopers\MangopayBundle\Event;

use Troopers\MangopayBundle\Entity\Order;
use MangoPay\CardPreAuthorization;
use Symfony\Component\EventDispatcher\Event;

class PreAuthorisationEvent extends Event
{
    private $order;
    private $preAuth;

    public function __construct(Order $order, CardPreAuthorization $preAuth)
    {
        $this->order = $order;
        $this->preAuth = $preAuth;
    }

    /**
     * Get order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set order
     *
     * @param string $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
    /**
     * Get preAuth
     *
     * @return string
     */
    public function getPreAuth()
    {
        return $this->preAuth;
    }

    /**
     * Set preAuth
     *
     * @param string $preAuth
     *
     * @return $this
     */
    public function setPreAuth($preAuth)
    {
        $this->preAuth = $preAuth;

        return $this;
    }
}
