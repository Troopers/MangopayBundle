<?php

namespace Troopers\MangopayBundle\Helper;

use Doctrine\ORM\EntityManager;
use MangoPay\Mandate;
use MangoPay\Sorting;
use MangoPay\Tests\BankAccounts;
use MangoPay\Wallet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Troopers\MangopayBundle\Entity\BankInformationInterface;
use Troopers\MangopayBundle\Entity\LegalUserInterface;
use Troopers\MangopayBundle\Entity\NaturalUserInterface;
use Troopers\MangopayBundle\Entity\UserInterface;
use Troopers\MangopayBundle\Event\WalletEvent;
use Troopers\MangopayBundle\TroopersMangopayEvents;
use Troopers\MangopayBundle\Helper\User\UserHelper;

class MandateHelper
{
    private $mangopayHelper;

    public function __construct(MangopayHelper $mangopayHelper)
    {
        $this->mangopayHelper = $mangopayHelper;
    }

    /**
     * @param BankInformationInterface $user
     *
     * @return Mandate
     */
    public function findOrCreateMandate(BankInformationInterface $bankInformation, $returnUrl = 'http://example.com/')
    {
        $bankInformationId = $bankInformation->getMangoBankAccountId();
        $userId = $bankInformation->getUser()->getMangoUserId();
        $pagination = null;
        $mandates = $this->mangopayHelper->Users->GetMandatesForBankAccount($userId, $bankInformationId, $pagination, (new Sorting())->AddField('CreationDate', 'DESC'));
        
        if (empty($mandates)) {
            $mandate = $this->createMandateForBankInformation($bankInformation, $returnUrl);
        // else, create a new mango user
        } else {
            $mandate = array_shift($mandates);
        }

        return $mandate;
    }

    public function createMandateForBankInformation(BankInformationInterface $bankInformation, $returnUrl = 'http://example.com/')
    {
        $bankInformationId = $bankInformation->getMangoBankAccountId();
        $userId = $bankInformation->getUser()->getMangoUserId();

        $mandate = new Mandate();
        $mandate->BankAccountId = $bankInformationId;
        $user = $bankInformation->getUser();
        if ($user instanceof LegalUserInterface) {
            $culture = $user->getLegalRepresentativeNationality();
        } else {
            $culture = $user->getNationality();
        }
        $mandate->Culture = $culture;
        $mandate->ReturnURL = $returnUrl;
        $mangoMandate = $this->mangopayHelper->Mandates->Create($mandate, md5(json_encode([
            'bankInformation' => $bankInformationId,
            'user' => $userId,
        ])));

        $bankInformation->setMangoMandateId($mandate->Id);
        $bankInformation->setMangoMandateUrl($mandate->RedirectURL);

        return $mangoMandate;
    }
}
