<?php

namespace Troopers\MangopayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Transaction.
 *
 * @ORM\MappedSuperclass
 */
class Transaction implements TransactionInterface
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     */
    protected $mangoId;

    /**
     * Author Id.
     *
     * @var int
     * @ORM\Column(name="authorId", type="integer")
     */
    protected $authorId;

    /**
     * Credited user Id.
     *
     * @var int
     * @ORM\Column(name="creditedUserId", type="integer")
     */
    protected $creditedUserId;

    /**
     * Credited wallet Id.
     *
     * @var int
     * @ORM\Column(name="creditedWalletId", type="integer")
     */
    protected $creditedWalletId;

    /**
     * Debited funds.
     *
     * @var \MangoPay\Money
     * @ORM\Column(name="debitedFunds", type="integer")
     */
    protected $debitedFunds;

    /**
     * Credited funds.
     *
     * @var \MangoPay\Money
     * @ORM\Column(name="creditedFunds", type="integer")
     */
    protected $creditedFunds;

    /**
     * Fees.
     *
     * @var \MangoPay\Money
     * @ORM\Column(name="fees", type="integer")
     */
    protected $fees;

    /**
     * TransactionStatus {CREATED, SUCCEEDED, FAILED}.
     *
     * @var string
     * @ORM\Column(name="status", type="string", length=255)
     */
    protected $status;

    /**
     * Result code.
     *
     * @var string
     * @ORM\Column(name="resultCode", type="integer")
     */
    protected $resultCode;

    /**
     * The PreAuthorization result Message explaining the result code.
     *
     * @var string
     * @ORM\Column(name="resultMessage", type="string", length=255)
     */
    protected $resultMessage;

    /**
     * Execution date;.
     *
     * @var \DateTime
     * @ORM\Column(name="executionDate", type="datetime", nullable=true)
     */
    protected $executionDate;

    /**
     * TransactionType {PAYIN, PAYOUT, TRANSFER}.
     *
     * @var string
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * TransactionNature { REGULAR, REFUND, REPUDIATION }.
     *
     * @var string
     * @ORM\Column(name="nature", type="string", length=255)
     */
    protected $nature;

    /**
     * CardType { CB_VISA_MASTERCARD, MAESTRO, DINERS, P24″, MASTERPASS }.
     *
     * @var string
     * @ORM\Column(name="cardType", type="string", length=255, nullable=true)
     */
    protected $cardType;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMangoId()
    {
        return $this->mangoId;
    }

    /**
     * @param int $mangoId
     * @return Transaction
     */
    public function setMangoId($mangoId)
    {
        $this->mangoId = $mangoId;
        return $this;
    }

    /**
     * Get authorId.
     *
     * @return string
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Set authorId.
     *
     * @param string $authorId
     *
     * @return $this
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * Get creditedUserId.
     *
     * @return string
     */
    public function getCreditedUserId()
    {
        return $this->creditedUserId;
    }

    /**
     * Set creditedUserId.
     *
     * @param string $creditedUserId
     *
     * @return $this
     */
    public function setCreditedUserId($creditedUserId)
    {
        $this->creditedUserId = $creditedUserId;

        return $this;
    }

    /**
     * Get creditedWalletId.
     *
     * @return string
     */
    public function getCreditedWalletId()
    {
        return $this->creditedWalletId;
    }

    /**
     * Set creditedUserId.
     *
     * @param string $creditedWalletId
     *
     * @return $this
     */
    public function setCreditedWalletId($creditedWalletId)
    {
        $this->creditedWalletId = $creditedWalletId;

        return $this;
    }

    /**
     * Get debitedFunds.
     *
     * @return string
     */
    public function getDebitedFunds()
    {
        return $this->debitedFunds;
    }

    /**
     * Set debitedFunds.
     *
     * @param string $debitedFunds
     *
     * @return $this
     */
    public function setDebitedFunds($debitedFunds)
    {
        $this->debitedFunds = $debitedFunds;

        return $this;
    }

    /**
     * Get creditedFunds.
     *
     * @return string
     */
    public function getCreditedFunds()
    {
        return $this->creditedFunds;
    }

    /**
     * Set creditedFunds.
     *
     * @param string $creditedFunds
     *
     * @return $this
     */
    public function setCreditedFunds($creditedFunds)
    {
        $this->creditedFunds = $creditedFunds;

        return $this;
    }

    /**
     * Get fees.
     *
     * @return string
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * Set fees.
     *
     * @param string $fees
     *
     * @return $this
     */
    public function setFees($fees)
    {
        $this->fees = $fees;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get resultCode.
     *
     * @return string
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * Set resultCode.
     *
     * @param string $resultCode
     *
     * @return $this
     */
    public function setResultCode($resultCode)
    {
        $this->resultCode = $resultCode;

        return $this;
    }

    /**
     * Get resultMessage.
     *
     * @return string
     */
    public function getResultMessage()
    {
        return $this->resultMessage;
    }

    /**
     * Set resultMessage.
     *
     * @param string $resultMessage
     *
     * @return $this
     */
    public function setResultMessage($resultMessage)
    {
        $this->resultMessage = $resultMessage;

        return $this;
    }

    /**
     * Get executionDate.
     *
     * @return string
     */
    public function getExecutionDate()
    {
        return $this->executionDate;
    }

    /**
     * Set executionDate.
     *
     * @param string $executionDate
     *
     * @return $this
     */
    public function setExecutionDate($executionDate)
    {
        $this->executionDate = $executionDate;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get nature.
     *
     * @return string
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set nature.
     *
     * @param string $nature
     *
     * @return $this
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get card type.
     *
     * @return string
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * Set card type.
     *
     * @param string $cardType
     *
     * @return $this
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;

        return $this;
    }
}
