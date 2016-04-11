<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\UserEvent;
use Doctrine\ORM\EntityManager;
use MangoPay\CardRegistration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * ref: appventus_mangopay.card_registration_helper
 *
 **/
class CardRegistrationHelper
{
    private $mangopayHelper;
    private $entityManager;
    private $dispatcher;

    public function __construct(MangopayHelper $mangopayHelper, EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    public function createCardRegistrationForUser(UserInterface $user)
    {

        $cardRegistration = new CardRegistration();
        $cardRegistration->userId = $user->getMangoUserId();
        $cardRegistration->Tag = 'user id : '.$user->getId();

        $cardRegistration = $this->mangopayHelper->CardRegistrations->Create($cardRegistration);

        return $cardRegistration;
    }
}
