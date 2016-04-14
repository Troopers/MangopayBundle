<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\UserInterface;
use AppVentus\MangopayBundle\Event\UserEvent;
use Doctrine\ORM\EntityManager;
use MangoPay\User;
use MangoPay\UserNatural;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * ref: appventus_mangopay.user_helper
 *
 **/
class UserHelper
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

    public function findOrCreateMangoUser(UserInterface $user)
    {
        if ($mangoUserId = $user->getMangoPayInfo()->getUserNaturalId()) {
            $mangoUser = $this->mangopayHelper->Users->get($mangoUserId);
        } else {
            $mangoUser = $this->createMangoUser($user);
        }

        return $mangoUser;
    }

    public function createMangoUser(UserInterface $user)
    {

        $mangoUser = new UserNatural();
        $mangoUser->Email = $user->getEmail();
        $mangoUser->FirstName = $user->getFirstname();
        $mangoUser->LastName = $user->getLastname();
        $mangoUser->Birthday = $user->getBirthDate();
        $mangoUser->Nationality = $user->getNationality();
        $mangoUser->CountryOfResidence = $user->getCountry();
        $mangoUser->Tag = $user->getId();

        $mangoUser = $this->mangopayHelper->Users->Create($mangoUser);

        $event = new UserEvent($user, $mangoUser);
        $this->dispatcher->dispatch(AppVentusMangopayEvents::NEW_USER, $event);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $mangoUser;
    }

    public function updateMangoUser(UserInterface $user)
    {
        $mangoUserId = $user->getMangoUserId();
        $mangoUser = $this->mangopayHelper->Users->get($mangoUserId);

        $mangoUser->Email = $user->getEmail();
        $mangoUser->FirstName = $user->getFirstname();
        $mangoUser->LastName = $user->getLastname();
        $mangoUser->Birthday = $user->getBirthDate();
        $mangoUser->Nationality = $user->getNationality();
        $mangoUser->CountryOfResidence = $user->getCountry();
        $mangoUser->Tag = $user->getId();

        $mangoUser = $this->mangopayHelper->Users->Update($mangoUser);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $mangoUser;
    }
}
