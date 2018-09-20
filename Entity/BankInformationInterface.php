<?php

namespace Troopers\MangopayBundle\Entity;

/**
 * Defines mandatory methods BankInformation need to be used in Mango
 * https://docs.mangopay.com/api-references/users/natural-users/.
 */
interface BankInformationInterface
{
    /**
     * BankInformation address.
     *
     * @var string
     */
    public function getBankInformationStreetAddress();

    /**
     * BankInformation address.
     *
     * @var string
     */
    public function getBankInformationAdditionalStreetAddress();

    /**
     * BankInformation address.
     *
     * @var string
     */
    public function getBankInformationCity();

    /**
     * BankInformation address.
     *
     * @var string
     */
    public function getBankInformationPostalCode();

    /**
     * BankInformation address.
     *
     * @var string
     */
    public function getBankInformationCountry();

    /**
     * BankInformation name.
     *
     * @var string
     */
    public function getBankInformationFullName();

    /**
     * It represents the amount debited on the bank account of the Author.In cents so 100€ will be written like « Amount » : 10000
     * DebitedFunds – Fees = CreditedFunds (amount received on wallet).
     *
     * @var string
     */
    public function getIban();

    /**
     * The user bank informations belongs to
     *
     * @var UserInterface
     */
    public function getUser();

    public function getMangoBankAccountId();

    public function setMangoBankAccountId($mangoBankAccountId);
}
