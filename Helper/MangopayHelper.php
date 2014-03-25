<?php
namespace Yosh\MangopayBundle\Service;

use MangoPay\BankAccount;
use MangoPay\BankAccountDetailsIBAN;
use MangoPay\CardRegistration;
use MangoPay\MangoPay;
use MangoPay\UserNatural;
use Yosh\AppBundle\Entity\User\Yosher;

/**
 * This class is an interface between raw MongoPay api object and symfony2.
 * It is declared as a service with application wide "clientId", "clientPassword" and "baseUrl" parameters
 * This service provides some shortcuts to interact with the api
 *
 * You can use it threw "appventus_mangopay.mango_api" service.
 *
 **/
class MangoPayHelper extends MangoPay
{
    protected $clientId;
    protected $clientPassword;
    protected $baseUrl;

    public function __construct($clientId, $clientPassword, $baseUrl)
    {
        parent::__construct();
        $this->Config->ClientId = $clientId;
        $this->Config->ClientPassword = $clientPassword;
        $this->Config->TemporaryFolder = sys_get_temp_dir();
        $this->Config->BaseUrl = $baseUrl;
    }
}
