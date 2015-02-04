<?php

namespace AppVentus\MangopayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Order
 *
 * @ORM\MappedSuperclass
 */
class Order
{
    use TimestampableEntity;

    const STATUS_CANCELED            = 'canceled';
    const STATUS_WAITING_FOR_PAYMENT = 'waiting';
    const STATUS_PENDING             = 'pending';
    const STATUS_PAID                = 'paid';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=15)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="Nooster\CoreBundle\Entity\Order\Transaction", cascade={"remove"})
     * @ORM\JoinColumn(name="payin_transaction_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $payinTransaction;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->message = new \Doctrine\Common\Collections\ArrayCollection();
        $this->status = self::STATUS_WAITING_FOR_PAYMENT;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set payinTransaction
     *
     * @param integer $payinTransaction
     *
     * @return Order
     */
    public function setPayinTransaction($payinTransaction)
    {
        $this->payinTransaction = $payinTransaction;

        return $this;
    }

    /**
     * Get payinTransaction
     *
     * @return integer
     */
    public function getPayinTransaction()
    {
        return $this->payinTransaction;
    }

}
