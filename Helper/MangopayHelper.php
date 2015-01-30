<?php
namespace AppVentus\MangopayBundle\Helper;

use AppVentus\MangopayBundle\Entity\UserInterface;
use MangoPay\MangoPayApi;
use MangoPay\UserNatural;

/**
 * This class is an interface between raw MongoPay api object and symfony2.
 * It is declared as a service with application wide "clientId", "clientPassword" and "baseUrl" parameters
 * This service provides some shortcuts to interact with the api
 *
 * You can use it threw "appventus_mangopay.mango_api" service.
 *
 **/
class MangopayHelper extends MangoPayApi
{
    protected $clientId;
    protected $clientPassword;
    protected $baseUrl;
    protected $dispatcher;

    public function __construct($clientId, $clientPassword, $baseUrl, $dispatcher, $debug = false)
    {
        parent::__construct($debug);
        $this->Config->ClientId = $clientId;
        $this->Config->ClientPassword = $clientPassword;
        $this->Config->TemporaryFolder = sys_get_temp_dir();
        $this->Config->BaseUrl = $baseUrl;
        $this->dispatcher = $dispatcher;
    }

    public function createMangoUser(UserInterface $user)
    {
        $mangoUser = new UserNatural();
        $mangoUser->Email = $user->getEmail();
        $mangoUser->FirstName = $user->getFirstname();
        $mangoUser->LastName = $user->getLastname();
        $mangoUser->Birthday = $user->getBirthDate()->getTimestamp();
        $mangoUser->Nationality = $user->getNationality();
        $mangoUser->CountryOfResidence = $user->getCountry();

        $mangoUser = $this->Users->Create($mangoUser);
    }
    public function findOrCreateMangoUser(UserInterface $user)
    {
        if ($user->getMangoUserId()) {
            $mangoUser = $this->Users->get($user->getMangoUserId());
        // else, create a new mango user
        } else {
            $this->createMangoUser($user);
        }

        return $mangoUser;
    }
}
