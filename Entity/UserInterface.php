<?php

namespace Troopers\MangopayBundle\Entity;

/**
 * Defines mandatory methods a Mango user should have
 * https://docs.mangopay.com/api-references/users/natural-users/.
 */
interface UserInterface
{
    /**
     * @var int
     */
    public function getId();

    /**
     * @var int
     */
    public function getMangoUserId();

    /**
     * @var int
     */
    public function getMangoWalletId();

    /**
     * @var int
     */
    public function getCardId();

    /**
     * @var int
     */
    public function getBankAccountId();

    /**
     * @var string
     *             User’s e-mail. A correct email address is expected
     */
    public function getEmail();

    /**
     * @var string
     *             User’s firstname (<100 chars)
     */
    public function getFirstname();

    /**
     * @var string
     *             User’s lastname (<100 chars)
     */
    public function getLastname();

    /**
     * @var date
     *           User’s birthdate.
     */
    public function getBirthDate();

    /**
     * @var string
     *             User’s Nationality. ISO 3166-1 alpha-2 format is expected
     */
    public function getNationality();

    /**
     * @var string
     *             User’s country of residence. ISO 3166-1 alpha-2 format is expected
     */
    public function getCountry();
}
