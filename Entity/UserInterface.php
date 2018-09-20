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

    /**
     * @var int
     */
    public function getMangoWalletId();

    /**
     * @var int
     */
    public function getMangoCardId();

    /**
     * @var int
     */
    public function getMangoBankAccountId();
}
