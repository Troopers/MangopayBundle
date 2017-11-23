<?php

namespace Troopers\MangopayBundle\Helper;

use Doctrine\ORM\EntityManager;
use MangoPay\UserLegal;
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

    /**
     * @param UserInterface $user
     * @param string $userType | NATURAL, BUSINESS, ORGANIZATION
     *
     * @return UserLegal|UserNatural
     */
    public function createMangoUser(UserInterface $user, $userType = 'NATURAL')
    {
        $birthdate = null;
        if ($user->getBirthDate() instanceof \Datetime) {
            $birthdate = $user->getBirthDate()->getTimestamp();
        }

        if (in_array($userType, ['BUSINESS', 'ORGANIZATION']) ) {
            $mangoUser = new UserLegal();
            $mangoUser->LegalPersonType = $userType;
            $mangoUser->Name = $user->getLastname().' '.$user->getFirstname();
            $mangoUser->Email = $user->getEmail();
            $mangoUser->LegalRepresentativeFirstName = $user->getFirstname();
            $mangoUser->LegalRepresentativeLastName = $user->getLastname();
            $mangoUser->LegalRepresentativeBirthday = $birthdate;
            $mangoUser->LegalRepresentativeNationality = $user->getNationality();
            $mangoUser->LegalRepresentativeCountryOfResidence = $user->getCountry();
            $mangoUser->Tag = $user->getId();
        } elseif ($userType === 'NATURAL') {
            $mangoUser = new UserNatural();
            $mangoUser->Email = $user->getEmail();
            $mangoUser->FirstName = $user->getFirstname();
            $mangoUser->LastName = $user->getLastname();
            $mangoUser->Birthday = $birthdate;
            $mangoUser->Nationality = $user->getNationality();
            $mangoUser->CountryOfResidence = $user->getCountry();
            $mangoUser->Tag = $user->getId();
        } else {
            throw new \InvalidArgumentException(sprintf('Invalid argument, userType must be equal to NATURAL, BUSINESS or ORGANIZATION, %s given', $userType));
        }

        $mangoUser = $this->mangopayHelper->Users->Create($mangoUser);

        $event = new UserEvent($user, $mangoUser);
        $this->dispatcher->dispatch(TroopersMangopayEvents::NEW_USER, $event);

        return $mangoUser;
    }

    /**
     * @param UserInterface $user
     * @param string $userType | NATURAL, BUSINESS, ORGANIZATION
     *
     * @return UserLegal|UserNatural
     */
    public function updateMangoUser(UserInterface $user, $userType = 'NATURAL')
    {
        if ($user->getBirthDate() instanceof \Datetime) {
            $birthdate = $user->getBirthDate()->getTimestamp();
        }
        $mangoUserId = $user->getMangoUserId();
        $mangoUser = $this->mangopayHelper->Users->get($mangoUserId);

        $userAddress = $user->getAddress();
        $city = $user->getCity();
        $postalCode = $user->getPostalCode();
        $address = new \MangoPay\Address();
        $address->AddressLine1 = $userAddress;
        $address->City = $city;
        $address->Country = $user->getCountry();
        $address->PostalCode = $postalCode;

        if ($mangoUser instanceof UserNatural) {
            $mangoUser->Email = $user->getEmail();
            $mangoUser->Tag = $user->getId();
            $mangoUser->FirstName = $user->getFirstname();
            $mangoUser->LastName = $user->getLastname();
            $mangoUser->Birthday = isset($birthdate) ? $birthdate : null;
            $mangoUser->Nationality = $user->getNationality();
            $mangoUser->CountryOfResidence = $user->getCountry();
            $mangoUser->Address = $address;
        } elseif ($mangoUser instanceof UserLegal) {
            $mangoUser->LegalPersonType = $userType;
            $mangoUser->Email = $user->getEmail();
            $mangoUser->Tag = $user->getId();
            $mangoUser->Name = $user->getLastname().' '.$user->getFirstname();
            $mangoUser->Email = $user->getEmail();
            $mangoUser->LegalRepresentativeFirstName = $user->getFirstname();
            $mangoUser->LegalRepresentativeLastName = $user->getLastname();
            $mangoUser->LegalRepresentativeBirthday = $birthdate;
            $mangoUser->LegalRepresentativeNationality = $user->getNationality();
            $mangoUser->LegalRepresentativeCountryOfResidence = $user->getCountry();
            $mangoUser->LegalRepresentativeAddress = $address;

        }

        $mangoUser = $this->mangopayHelper->Users->Update($mangoUser);

        //@TODO: remove this, it's not bundle responsibility
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $mangoUser;
    }

    public function getTransactions($userId)
    {
        return $this->mangopayHelper->Users->GetTransactions($userId);
    }
}
