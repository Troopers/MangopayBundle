<?php

namespace Troopers\MangopayBundle\Helper\User;

use Doctrine\ORM\EntityManager;
use MangoPay\KycDocument;
use MangoPay\KycDocumentStatus;
use MangoPay\KycLevel;
use MangoPay\KycPage;
use MangoPay\User;
use MangoPay\UserLegal;
use MangoPay\UserNatural;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;
use Troopers\MangopayBundle\Entity\BankInformationInterface;
use Troopers\MangopayBundle\Entity\LegalUserInterface;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Event\UserEvent;
use Troopers\MangopayBundle\Helper\KYCHelper;
use Troopers\MangopayBundle\TroopersMangopayEvents;
use Troopers\MangopayBundle\Helper\MangopayHelper;
use MangoPay\BankAccount;
use MangoPay\BankAccountDetailsIBAN;
use MangoPay\KycDocumentType;

class LegalUserHelper
{
    private $mangopayHelper;
    private $dispatcher;
    private $KYCHelper;
    private $mangopaySandbox;

    public function __construct(MangopayHelper $mangopayHelper, EventDispatcherInterface $dispatcher, KYCHelper $KYCHelper, $mangopaySandbox)
    {
        $this->mangopayHelper = $mangopayHelper;
        $this->dispatcher = $dispatcher;
        $this->KYCHelper = $KYCHelper;
        $this->mangopaySandbox = $mangopaySandbox;
    }

    public function createMangoUser(UserInterface $user)
    {
        $birthday = null;
        if ($user->getLegalRepresentativeBirthday() instanceof \Datetime) {
            $birthday = $user->getLegalRepresentativeBirthday();
        } else if (null !== $user->getLegalRepresentativeBirthday()) {
            $birthday = new \DateTime($user->getLegalRepresentativeBirthday());
        }
        $mangoUser = new UserLegal();
        $mangoUser->LegalPersonType = $user->getLegalPersonType();
        $mangoUser->Name = $user->getName();
        $mangoUser->Email = $user->getEmail();
        $legalRepresentativeFirstName = $user->getLegalRepresentativeFirstName();
        if ($this->mangopaySandbox) {
            $legalRepresentativeFirstName = "Successful";
        }
        $mangoUser->LegalRepresentativeFirstName = $legalRepresentativeFirstName;
        $mangoUser->LegalRepresentativeLastName = $user->getLegalRepresentativeLastName();
        $mangoUser->LegalRepresentativeBirthday = $birthday ? $birthday->getTimestamp() : null;
        $mangoUser->LegalRepresentativeNationality = $user->getLegalRepresentativeNationality();
        $mangoUser->LegalRepresentativeCountryOfResidence = $user->getLegalRepresentativeCountryOfResidence();

        $address = new \MangoPay\Address();
        $address->AddressLine1 = $user->getLegalRepresentativeStreetAddress();
        $address->AddressLine2 = $user->getLegalRepresentativeAdditionalStreetAddress();
        $address->City = $user->getLegalRepresentativeCity();
        $address->Country = $user->getLegalRepresentativeCountry();
        $address->PostalCode = $user->getLegalRepresentativePostalCode();

        $mangoUser->Address = $address;

        $headQuartersAddress = new \MangoPay\Address();
        $headQuartersAddress->AddressLine1 = $user->getHeadquartersStreetAddress();
        $headQuartersAddress->AddressLine2 = $user->getHeadquartersAdditionalStreetAddress();
        $headQuartersAddress->City = $user->getHeadquartersCity();
        $headQuartersAddress->Country = $user->getHeadquartersCountry();
        $headQuartersAddress->PostalCode = $user->getHeadquartersPostalCode();

        $mangoUser->HeadquartersAddress = $headQuartersAddress;

        $mangoUser = $this->mangopayHelper->Users->Create($mangoUser);
        $user->setMangoUserId($mangoUser->Id);

        if (null !== $document = $user->getProofOfRegistration()) {
            $mangoDocument = $this->createDocument($document, $user, KycDocumentType::RegistrationProof);
            $mangoUser->ProofOfRegistration = $mangoDocument->Id;
            $user->setProofOfRegistrationId($mangoDocument->Id);
        }

        if (null !== $document = $user->getLegalRepresentativeProofOfIdentity()) {
            $mangoDocument = $this->createDocument($document, $user, KycDocumentType::IdentityProof);
            $mangoUser->LegalRepresentativeProofOfIdentity = $mangoDocument->Id;
            $user->setLegalRepresentativeProofOfIdentityId($mangoDocument->Id);
        }

        if (null !== $document = $user->getStatute()) {
            $mangoDocument = $this->createDocument($document, $user, KycDocumentType::ArticlesOfAssociation);
            $mangoUser->Statute = $mangoDocument->Id;
            $user->setStatuteId($mangoDocument->Id);
        }

        if (null !== $document = $user->getShareholderDeclaration()) {
            $mangoDocument = $this->createDocument($document, $user, KycDocumentType::ShareholderDeclaration);
            $mangoUser->ShareholderDeclaration = $mangoDocument->Id;
            $user->setShareholderDeclarationId($mangoDocument->Id);
        }

        $event = new UserEvent($user, $mangoUser);
        $this->dispatcher->dispatch(TroopersMangopayEvents::NEW_USER, $event);

        return $mangoUser;
    }

    public function updateMangoUser(UserInterface $user)
    {
        $birthday = null;
        if ($user->getLegalRepresentativeBirthday() instanceof \Datetime) {
            $birthday = $user->getLegalRepresentativeBirthday()->getTimestamp();
        }

        $mangoUserId = $user->getMangoUserId();
        $mangoUser = $this->mangopayHelper->Users->get($mangoUserId);

        $mangoUser->Email = $user->getEmail();
        $mangoUser->LegalRepresentativeFirstName = $user->getLegalRepresentativeFirstName();
        $mangoUser->LegalRepresentativeLastName = $user->getLegalRepresentativeLastName();
        $mangoUser->LegalRepresentativeBirthday = $birthday;
        $mangoUser->LegalRepresentativeNationality = $user->getLegalRepresentativeNationality();
        $mangoUser->LegalRepresentativeCountryOfResidence = $user->getLegalRepresentativeCountryOfResidence();
        $mangoUser->Tag = $user->getId();

        $address = new \MangoPay\Address();
        $address->AddressLine1 = $user->getLegalRepresentativeStreetAddress();
        $address->City = $user->getLegalRepresentativeCity();
        $address->Country = $user->getLegalRepresentativeCountry();
        $address->PostalCode = $user->getLegalRepresentativePostalCode();

        $mangoUser->Address = $address;


        if (null !== $document = $user->getProofOfRegistration()) {
            $mangoDocument = $this->createDocument($document, $user, KycDocumentType::RegistrationProof);
            $mangoUser->ProofOfRegistration = $mangoDocument->Id;
            $user->setProofOfRegistrationId($mangoDocument->Id);
        }

        if (null !== $document = $user->getLegalRepresentativeProofOfIdentity()) {
            $mangoDocument = $this->createDocument($document, $user, KycDocumentType::IdentityProof);
            $mangoUser->LegalRepresentativeProofOfIdentity = $mangoDocument->Id;
            $user->setLegalRepresentativeProofOfIdentityId($mangoDocument->Id);
        }

        if (null !== $document = $user->getStatute()) {
            $mangoDocument = $this->createDocument($document, $user, KycDocumentType::ArticlesOfAssociation);
            $mangoUser->Statute = $mangoDocument->Id;
            $user->setStatuteId($mangoDocument->Id);
        }

        if (null !== $document = $user->getShareholderDeclaration()) {
            $mangoDocument = $this->createDocument($document, $user, KycDocumentType::ShareholderDeclaration);
            $mangoUser->ShareholderDeclaration = $mangoDocument->Id;
            $user->setShareholderDeclarationId($mangoDocument->Id);
        }

        $mangoUser = $this->mangopayHelper->Users->Update($mangoUser);

        return $mangoUser;
    }

    protected function createDocument($fileContent, UserInterface $user, $type)
    {
        $kycDocument = new KycDocument();
        $kycDocument->UserId = $user->getMangoUserId();
        $kycDocument->Type = $type;

        $document = $this->mangopayHelper->Users->CreateKycDocument($user->getMangoUserId(), $kycDocument);

        $page = new KycPage();
        $page->File = $fileContent;

        $this->mangopayHelper->Users->CreateKycPage($user->getMangoUserId(), $document->Id, $page);

        $document->Status = KycDocumentStatus::ValidationAsked;

        $this->mangopayHelper->Users->UpdateKycDocument($user->getMangoUserId(), $document);

        return $document;
    }
}
