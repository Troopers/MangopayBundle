<?php

namespace Troopers\MangopayBundle\Entity;

/**
 * Defines mandatory methods BankInformation need to be used in Mango
 * https://docs.mangopay.com/api-references/users/natural-users/
 */
interface BankInformationInterface
{
    /**
     * Author Mango Id
     * @var string
     */
    public function getAddress();

    /**
     * It represents the amount debited on the bank account of the Author.In cents so 100€ will be written like « Amount » : 10000
     * DebitedFunds – Fees = CreditedFunds (amount received on wallet)
     *
     * @var string
     */
    public function getIban();

}
