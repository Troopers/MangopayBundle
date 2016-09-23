<?php

namespace Troopers\MangopayBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Troopers\MangopayBundle\Entity\Order;

class OrderEvent extends Event
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
