<?php

namespace AppVentus\MangopayBundle\Entity;

/**
 * Defines mandatory methods a Transaction need to be used in Mango
 * https://docs.mangopay.com/api-references/users/natural-users/
 */
interface TransactionInterface
{
    /**
     * Author Mango Id
     * @var string
     */
    public function getAuthorMangoId();
    
    /**
     * It represents the amount debited on the bank account of the Author.In cents so 100€ will be written like « Amount » : 10000
     * DebitedFunds – Fees = CreditedFunds (amount received on wallet)
     * 
     * @var string
     */
    public function getDebitedFunds();
    
    /**
     * It represents your fees taken on the DebitedFunds.In cents so 100€ will be written like « Amount » : 10000
     * 
     * @var integer
     */
    public function getFees();
    
    /**
     * The Mango ID of the credited wallet
     * 
     * @var integer
     */
    public function getCreditedWalletId();
    
    /**
     * URL Format expected
     * 
     * @var integer
     */
    public function getCardType();

}
