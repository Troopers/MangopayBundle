<?php

namespace AppVentus\MangopayBundle\Helper;

use Doctrine\ORM\EntityManager;
use MangoPay\MangoPayApi;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This class is an interface between raw MongoPay api object and symfony2.
 * It is declared as a service with application wide "clientId", "clientPassword" and "baseUrl" parameters
 * This service provides some shortcuts to interact with the api
 *
 * ref: appventus_mangopay.mango_api
 *
 **/
class MangopayHelper extends MangoPayApi
{
    protected $clientId;
    protected $clientPassword;
    protected $baseUrl;
    protected $dispatcher;
    protected $entityManager;
    protected $debug;

    public function __construct($clientId, $clientPassword, $baseUrl, EventDispatcherInterface $dispatcher, EntityManager $entityManager, $debug = false)
    {
        parent::__construct();
        $this->Config->ClientId = $clientId;
        $this->Config->ClientPassword = $clientPassword;
        $this->Config->TemporaryFolder = sys_get_temp_dir();
        $this->Config->BaseUrl = $baseUrl;
        $this->Config->DebugMode = $debug;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
    }
    

}
