<?php

namespace Troopers\MangopayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * CardPreAuthorisation.
 *
 * @ORM\MappedSuperclass
 */
class CardPreAuthorisation
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
     * @var int Unique identifier
     *          (At this moment type is Integer - in the feature will be GUID)
     * @ORM\Column(name="mango_id", type="integer")
     */
    protected $mangoId;

    /**
     * @var int Unique identifier
     *          (At this moment type is Integer - in the feature will be GUID)
     * @ORM\Column(name="tag", type="string", length=255, nullable=true)
     */
    protected $tag;

    /**
     * @var int Unique identifier
     *          (At this moment type is Integer - in the feature will be GUID)
     * @ORM\Column(name="creation_date", type="datetime", nullable=true)
     */
    protected $creationDate;

    /**
     * The user Id of the author of the pre-authorization.
     *
     * @var string
     * @ORM\Column(name="author_id", type="integer", nullable=true)
     */
    protected $authorId;

    /**
     * It represents the amount debited on the bank account
     * of the Author.DebitedFunds = Fees + CreditedFunds
     * (amount received on wallet).
     *
     *  @var \MangoPay\Money
     * @ORM\Column(name="debited_funds", type="integer", nullable=true)
     */
    protected $debitedFunds;

    /**
     * Status of the PreAuthorization: CREATED, SUCCEEDED, FAILED.
     *
     * @var string
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;

    /**
     * The status of the payment after the PreAuthorization:
     * WAITING, CANCELED, EXPIRED, VALIDATED.
     *
     * @var string
     * @ORM\Column(name="payment_status", type="string", length=255, nullable=true)
     */
    protected $paymentStatus;

    /**
     * The PreAuthorization result code.
     *
     * @var string
     * @ORM\Column(name="result_code", type="integer", nullable=true)
     */
    protected $resultCode;

    /**
     * The PreAuthorization result Message explaining the result code.
     *
     * @var string
     * @ORM\Column(name="result_message", type="string", length=255, nullable=true)
     */
    protected $resultMessage;

    /**
     * How the PreAuthorization has been executed.
     * Only on value for now: CARD.
     *
     * @var string
     * @ORM\Column(name="execution_type", type="string", length=255, nullable=true)
     */
    protected $executionType;

    /**
     * The SecureMode correspond to '3D secure' for CB Visa and MasterCard
     * or 'Amex Safe Key' for American Express.
     * This field lets you activate it manually.
     *
     * @var string
     * @ORM\Column(name="secure_mode", type="string", length=255, nullable=true)
     */
    protected $secureMode;

    /**
     * The ID of the registered card (Got through CardRegistration object).
     *
     * @var string
     * @ORM\Column(name="card_id", type="integer", nullable=true)
     */
    protected $cardId;

    /**
     * Boolean. The value is 'true' if the SecureMode was used.
     *
     * @var string
     * @ORM\Column(name="secure_mode_needed", type="string", length=255, nullable=true)
     */
    protected $secureModeNeeded;

    /**
     * This is the URL where to redirect users to proceed
     * to 3D secure validation.
     *
     * @var string
     * @ORM\Column(name="secure_mode_redirect_url", type="string", length=255, nullable=true)
     */
    protected $secureModeRedirectURL;

    /**
     * This is the URL where users are automatically redirected
     * after 3D secure validation (if activated).
     *
     * @var string
     * @ORM\Column(name="secure_mode_return_url", type="string", length=255, nullable=true)
     */
    protected $secureModeReturnURL;

    /**
     * The date when the payment is processed.
     *
     * @var Timestamp
     * @ORM\Column(name="expiration_date", type="datetime", nullable=true)
     */
    protected $expirationDate;

    /**
     * The date when the payment was authorized.
     *
     * @var Timestamp
     * @ORM\Column(name="authorization_date", type="datetime", nullable=true)
     */
    protected $authorizationDate;

    /**
     * The type of pre-authorization ("CARD" is the only acceptable value at present.
     *
     * @var string
     * @ORM\Column(name="payment_type", type="string", length=255, nullable=true)
     */
    protected $paymentType;

    /**
     * The Id of the associated PayIn.
     *
     * @var string
     * @ORM\Column(name="pay_in_id", type="integer", nullable=true)
     */
    protected $payInId;

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
     * Get paymentStatus.
     *
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set paymentStatus.
     *
     * @param string $paymentStatus
     *
     * @return $this
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

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
     * Get executionType.
     *
     * @return string
     */
    public function getExecutionType()
    {
        return $this->executionType;
    }

    /**
     * Set executionType.
     *
     * @param string $executionType
     *
     * @return $this
     */
    public function setExecutionType($executionType)
    {
        $this->executionType = $executionType;

        return $this;
    }
    /**
     * Get secureMode.
     *
     * @return string
     */
    public function getSecureMode()
    {
        return $this->secureMode;
    }

    /**
     * Set secureMode.
     *
     * @param string $secureMode
     *
     * @return $this
     */
    public function setSecureMode($secureMode)
    {
        $this->secureMode = $secureMode;

        return $this;
    }
    /**
     * Get cardId.
     *
     * @return string
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * Set cardId.
     *
     * @param string $cardId
     *
     * @return $this
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;

        return $this;
    }
    /**
     * Get secureModeNeeded.
     *
     * @return string
     */
    public function getSecureModeNeeded()
    {
        return $this->secureModeNeeded;
    }

    /**
     * Set secureModeNeeded.
     *
     * @param string $secureModeNeeded
     *
     * @return $this
     */
    public function setSecureModeNeeded($secureModeNeeded)
    {
        $this->secureModeNeeded = $secureModeNeeded;

        return $this;
    }
    /**
     * Get secureModeRedirectURL.
     *
     * @return string
     */
    public function getSecureModeRedirectURL()
    {
        return $this->secureModeRedirectURL;
    }

    /**
     * Set secureModeRedirectURL.
     *
     * @param string $secureModeRedirectURL
     *
     * @return $this
     */
    public function setSecureModeRedirectURL($secureModeRedirectURL)
    {
        $this->secureModeRedirectURL = $secureModeRedirectURL;

        return $this;
    }
    /**
     * Get secureModeReturnURL.
     *
     * @return string
     */
    public function getSecureModeReturnURL()
    {
        return $this->secureModeReturnURL;
    }

    /**
     * Set secureModeReturnURL.
     *
     * @param string $secureModeReturnURL
     *
     * @return $this
     */
    public function setSecureModeReturnURL($secureModeReturnURL)
    {
        $this->secureModeReturnURL = $secureModeReturnURL;

        return $this;
    }
    /**
     * Get expirationDate.
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set expirationDate.
     *
     * @param string $expirationDate
     *
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }
    /**
     * Get authorizationDate.
     *
     * @return string
     */
    public function getAuthorizationDate()
    {
        return $this->authorizationDate;
    }

    /**
     * Set authorizationDate.
     *
     * @param string $authorizationDate
     *
     * @return $this
     */
    public function setAuthorizationDate($authorizationDate)
    {
        $this->authorizationDate = $authorizationDate;

        return $this;
    }
    /**
     * Get paymentType.
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set paymentType.
     *
     * @param string $paymentType
     *
     * @return $this
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }
    /**
     * Get payInId.
     *
     * @return string
     */
    public function getPayInId()
    {
        return $this->payInId;
    }

    /**
     * Set payInId.
     *
     * @param string $payInId
     *
     * @return $this
     */
    public function setPayInId($payInId)
    {
        $this->payInId = $payInId;

        return $this;
    }

    /**
     * Get mangoId.
     *
     * @return string
     */
    public function getMangoId()
    {
        return $this->mangoId;
    }

    /**
     * Set mangoId.
     *
     * @param string $mangoId
     *
     * @return $this
     */
    public function setMangoId($mangoId)
    {
        $this->mangoId = $mangoId;

        return $this;
    }
    /**
     * Get tag.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set tag.
     *
     * @param string $tag
     *
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }
    /**
     * Get creationDate.
     *
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set creationDate.
     *
     * @param string $creationDate
     *
     * @return $this
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }
}
