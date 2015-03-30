<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\CardPreAuthorisation;
use AppVentus\MangopayBundle\Entity\Order;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\CardRegistrationEvent;
use AppVentus\MangopayBundle\Event\PayInEvent;
use AppVentus\MangopayBundle\Event\PreAuthorisationEvent;
use MangoPay\CardPreAuthorization;
use MangoPay\CardRegistration;
use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInExecutionDetailsDirect;
use MangoPay\PayInPaymentDetailsPreAuthorized;
use MangoPay\User;
use MangoPay\Wallet;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * ref: appventus_mangopay.payment_direct_helper
 *
 **/
class PaymentDirectHelper
{
    private $mangopayHelper;
    private $router;
    private $dispatcher;

    public function __construct(MangopayHelper $mangopayHelper, Router $router, EventDispatcherInterface $dispatcher)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
    }
    
    protected function createDirectTransaction(UserInterface $user)
    {
        
        return 
    }

}
