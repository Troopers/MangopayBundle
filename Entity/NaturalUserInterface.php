<?php

namespace Troopers\MangopayBundle\Entity;

/**
 * Defines mandatory methods a Mango user should have
 * https://docs.mangopay.com/api-references/users/natural-users/.
 */
interface NaturalUserInterface extends UserInterface
{
    /**
     * @var string
     *             User’s firstname (<100 chars)
     */
    public function getFirstName();

    /**
     * @var string
     *             User’s lastname (<100 chars)
     */
    public function getLastName();

    /**
     * @var string
     *             Contatenation of firstname and lastname
     */
    public function getFullName();

    /**
     * @var string
     *             User’s address
     */
    public function getStreetAddress();

    /**
     * @var string
     *             User’s address
     */
    public function getAdditionalStreetAddress();

    /**
     * @var string
     *             User’s address
     */
    public function getCity();

    /**
     * @var string
     *             User’s address
     */
    public function getCountry();

    /**
     * @var string
     *             User’s address
     */
    public function getPostalCode();

    /**
     * @var \DateTime
     *           User’s birthdate.
     */
    public function getBirthday();

    /**
     * @var string
     *             User’s Nationality. ISO 3166-1 alpha-2 format is expected
     */
    public function getNationality();

    /**
     * @var string
     *             User’s country of residence. ISO 3166-1 alpha-2 format is expected
     */
    public function getCountryOfResidence();

    /**
     * @var string
     *             User’s occupation, optional.
     */
    public function getOccupation();

    /**
     * @var string
     *             User’s income range, optional.
     */
    public function getIncomeRange();

    /**
     * @var string
     *             User’s proof of identity
     */
    public function getProofOfIdentityId();

    /**
     * @var string
     *             User’s proof of address
     */
    public function getProofOfAddressId();

    /**
     * @var string
     *             User’s capacity, optional.
     */
    public function getCapacity();


}
