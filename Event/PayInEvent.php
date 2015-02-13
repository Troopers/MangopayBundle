<?php

namespace AppVentus\MangopayBundle\Event;

use MangoPay\PayIn;
use Symfony\Component\EventDispatcher\Event;

class PayInEvent extends Event
{
    private $payIn;
    private $preAuth;

    public function __construct(PayIn $payIn)
    {
        $this->payIn = $payIn;
    }

    /**
     * Get payin
     *
     * @return string
     */
    public function getPayin()
    {
        return $this->payin;
    }

    /**
     * Set payin
     *
     * @param string $payin
     *
     * @return $this
     */
    public function setPayin($payin)
    {
        $this->payin = $payin;

        return $this;
    }
}
