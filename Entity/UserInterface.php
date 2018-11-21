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
     * @var string
     *             User’s e-mail. A correct email address is expected
     */
    public function getEmail();

    /**
     * @var int
     */
    public function getMangoUserId();
    public function setMangoUserId($id);

    /**
     * @var int
     */
    public function getMangoWalletId();
    public function setMangoWalletId($id);

    /**
     * @var int
     */
    public function getMangoCardId();
    public function setMangoCardId($id);

    /**
     * @var int
     */
    public function getMangoBankAccountId();
    public function setMangoBankAccountId($id);

    /**
     * @var int
     */
    public function getBankInformation();
}
