<?php

namespace Troopers\MangopayBundle\Helper\User;

use Doctrine\ORM\EntityManager;
use MangoPay\UserNatural;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Troopers\MangopayBundle\Entity\NaturalUserInterface;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Event\UserEvent;
use Troopers\MangopayBundle\TroopersMangopayEvents;
use Troopers\MangopayBundle\Helper\MangopayHelper;
use MangoPay\BankAccount;
use MangoPay\BankAccountDetailsIBAN;
use Troopers\MangopayBundle\Entity\BankInformationInterface;

class NaturalUserHelper
{
    private $mangopayHelper;
    private $dispatcher;
    private $mangopaySandbox;

    public function __construct(MangopayHelper $mangopayHelper, EventDispatcherInterface $dispatcher, $mangopaySandbox)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->dispatcher = $dispatcher;
        $this->mangopaySandbox = $mangopaySandbox;
    }

    public function createMangoUser(NaturalUserInterface $user)
    {
        $birthday = null;
        if ($user->getBirthday() instanceof \Datetime) {
            $birthday = $user->getBirthday();
        } else if (null !== $user->getBirthday()) {
            $birthday = new \DateTime($user->getBirthday());
        }
        $mangoUser = new UserNatural();
        $mangoUser->Email = $user->getEmail();
        $firstname = $user->getFirstName();
        if ($this->mangopaySandbox) {
            $firstname = "Successful";
        }
        $mangoUser->FirstName = $firstname;
        $mangoUser->LastName = $user->getLastName();
        $mangoUser->Birthday = $birthday ? $birthday->getTimestamp() : null;
        $mangoUser->Nationality = $user->getNationality();
        $mangoUser->CountryOfResidence = $user->getCountry();
        $mangoUser->Tag = $user->getId();

        $mangoUser = $this->mangopayHelper->Users->Create($mangoUser);

        $event = new UserEvent($user, $mangoUser);
        $this->dispatcher->dispatch(TroopersMangopayEvents::NEW_USER, $event);

        return $mangoUser;
    }

    public function updateMangoUser(NaturalUserInterface $user)
    {

        if ($user->getBirthday() instanceof \Datetime) {
            $birthdate = $user->getBirthday()->getTimestamp();
        }
        $mangoUserId = $user->getMangoUserId();
        $mangoUser = $this->mangopayHelper->Users->get($mangoUserId);

        $mangoUser->Email = $user->getEmail();

        $firstname = $user->getFirstName();
        if ($this->mangopaySandbox) {
            $firstname = "Successful";
        }
        $mangoUser->FirstName = $firstname;
        $mangoUser->LastName = $user->getLastname();
        $mangoUser->Birthday = $birthdate;
        $mangoUser->Nationality = $user->getNationality();
        $mangoUser->CountryOfResidence = $user->getCountry();
        $mangoUser->Tag = $user->getId();

        $address = new \MangoPay\Address();
        $address->AddressLine1 = $user->getStreetAddress();
        $address->AddressLine2 = $user->getAdditionalStreetAddress();
        $address->City = $user->getCity();
        $address->Country = $user->getCountry();
        $address->PostalCode = $user->getPostalCode();

        $mangoUser->Address = $address;

        $mangoUser = $this->mangopayHelper->Users->Update($mangoUser);

        return $mangoUser;
    }
}
