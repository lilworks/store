<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_orders_paymentmethods")
 * @ORM\HasLifecycleCallbacks()
 */
class OrdersPaymentMethods
{
    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if( is_null($this->payedAt)  ){
            $this->payedAt= new \DateTime();
        }
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Many OrdersPaymentMethods have One Coupon.
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Coupon", inversedBy="ordersPaymentMethods")
     * @ORM\JoinColumn(name="coupon", referencedColumnName="id",nullable=true)
     */
    private $coupon;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Order", inversedBy="ordersPaymentMethods")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\PaymentMethod", inversedBy="ordersPaymentMethods")
     * @ORM\JoinColumn(name="paymentMethod", referencedColumnName="id", nullable=FALSE)
     * @Assert\NotBlank()
     */
    protected $paymentMethod;


    /**
     * @var datetime
     *
     * @ORM\Column(name="payedAt", type="datetime",nullable=true)
     */
    private $payedAt;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float",nullable=false)
     * @Assert\GreaterThan(0)
     */
    private $amount;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;



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
     * Set payedAt
     *
     * @param \DateTime $payedAt
     *
     * @return OrdersPaymentMethods
     */
    public function setPayedAt($payedAt)
    {
        $this->payedAt = $payedAt;

        return $this;
    }

    /**
     * Get payedAt
     *
     * @return \DateTime
     */
    public function getPayedAt()
    {
        return $this->payedAt;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return OrdersPaymentMethods
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return OrdersPaymentMethods
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set coupon
     *
     * @param \LilWorks\StoreBundle\Entity\Coupon $coupon
     *
     * @return OrdersPaymentMethods
     */
    public function setCoupon(\LilWorks\StoreBundle\Entity\Coupon $coupon = null)
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * Get coupon
     *
     * @return \LilWorks\StoreBundle\Entity\Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Set order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     *
     * @return OrdersPaymentMethods
     */
    public function setOrder(\LilWorks\StoreBundle\Entity\Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \LilWorks\StoreBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set paymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return OrdersPaymentMethods
     */
    public function setPaymentMethod(\LilWorks\StoreBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return \LilWorks\StoreBundle\Entity\PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
}
