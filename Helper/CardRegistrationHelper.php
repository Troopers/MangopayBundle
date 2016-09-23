<?php

namespace Troopers\MangopayBundle\Helper;

use Troopers\MangopayBundle\Entity\UserInterface;
use Doctrine\ORM\EntityManager;
use MangoPay\CardRegistration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * ref: troopers_mangopay.card_registration_helper.
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
        $cardRegistration->Currency = 'EUR';

        $cardRegistration = $this->mangopayHelper->CardRegistrations->Create($cardRegistration);

        return $cardRegistration;
    }
}
