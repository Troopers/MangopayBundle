<?php

namespace Troopers\MangopayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Order.
 *
 * @ORM\MappedSuperclass
 */
class Order
{
    use TimestampableEntity;
    /**
     * The offer was rejected.
     */
    const STATUS_CANCELED = 'canceled';
    /**
     * The buyer has not paied yet.
     */
    const STATUS_WAITING_FOR_PAYMENT = 'waiting';
    /**
     * The seller has not accepted or rejected the offer.
     */
    const STATUS_PENDING = 'pending';
    /**
     * The seller has accepted the order.
     */
    const STATUS_VALIDATED = 'validated';
    /**
     * The seller has paied the order.
     */
    const STATUS_PAID = 'paid';
    /**
     * The seller has paied the order.
     */
    const STATUS_WAITING_MANGOPAY_VALIDATION = 'waiting_mangopay_validation';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="Troopers\MangopayBundle\Entity\Transaction", cascade={"remove"})
     */
    protected $payinTransaction;

    /**
     * @var int
     *
     * @ORM\Column(name="mango_price", type="integer", nullable=true)
     */
    protected $mangoPrice;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->status = self::STATUS_WAITING_FOR_PAYMENT;
    }

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
     * Set status.
     *
     * @param string $status
     *
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * Set payinTransaction.
     *
     * @param int $payinTransaction
     *
     * @return Order
     */
    public function setPayinTransaction($payinTransaction)
    {
        $this->payinTransaction = $payinTransaction;

        return $this;
    }

    /**
     * Get payinTransaction.
     *
     * @return int
     */
    public function getPayinTransaction()
    {
        return $this->payinTransaction;
    }

    /**
     * Get mangoPrice.
     *
     * @return string
     */
    public function getMangoPrice()
    {
        return $this->mangoPrice;
    }

    /**
     * Set mangoPrice.
     *
     * @param string $mangoPrice
     *
     * @return $this
     */
    public function setMangoPrice($mangoPrice)
    {
        $this->mangoPrice = $mangoPrice;

        return $this;
    }
}
