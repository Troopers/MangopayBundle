<?php

namespace Troopers\MangopayBundle\Helper;

use Doctrine\ORM\EntityManager;
use MangoPay\UserNatural;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Event\UserEvent;
use Troopers\MangopayBundle\TroopersMangopayEvents;

/**
 * ref: troopers_mangopay.user_helper.
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
        if ($mangoUserId = $user->getMangoUserId()) {
            $mangoUser = $this->mangopayHelper->Users->get($mangoUserId);
        } else {
            $mangoUser = $this->createMangoUser($user);
        }

        return $mangoUser;
    }

    public function createMangoUser(UserInterface $user)
    {
        if ($user->getBirthDate() instanceof \Datetime) {
            $birthdate = $user->getBirthDate()->getTimestamp();
        }
        $mangoUser = new UserNatural();
        $mangoUser->Email = $user->getEmail();
        $mangoUser->FirstName = $user->getFirstname();
        $mangoUser->LastName = $user->getLastname();
        $mangoUser->Birthday = $birthdate;
        $mangoUser->Nationality = $user->getNationality();
        $mangoUser->CountryOfResidence = $user->getCountry();
        $mangoUser->Tag = $user->getId();

        $mangoUser = $this->mangopayHelper->Users->Create($mangoUser);

        $event = new UserEvent($user, $mangoUser);
        $this->dispatcher->dispatch(TroopersMangopayEvents::NEW_USER, $event);

        return $mangoUser;
    }

    public function updateMangoUser(UserInterface $user)
    {
        if ($user->getBirthDate() instanceof \Datetime) {
            $birthdate = $user->getBirthDate()->getTimestamp();
        }
        $mangoUserId = $user->getMangoUserId();
        $mangoUser = $this->mangopayHelper->Users->get($mangoUserId);

        $mangoUser->Email = $user->getEmail();
        $mangoUser->FirstName = $user->getFirstname();
        $mangoUser->LastName = $user->getLastname();
        $mangoUser->Birthday = $birthdate;
        $mangoUser->Nationality = $user->getNationality();
        $mangoUser->CountryOfResidence = $user->getCountry();
        $mangoUser->Tag = $user->getId();

        $userAddress = $user->getAddress();
        $city = $user->getCity();
        $postalCode = $user->getPostalCode();
        $address = new \MangoPay\Address();
        $address->AddressLine1 = $userAddress;
        $address->City = $city;
        $address->Country = $user->getCountry();
        $address->PostalCode = $postalCode;

        $mangoUser->Address = $address;

        $mangoUser = $this->mangopayHelper->Users->Update($mangoUser);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $mangoUser;
    }

    public function getTransactions($userId)
    {
        return $this->mangopayHelper->Users->GetTransactions($userId);
    }
}
