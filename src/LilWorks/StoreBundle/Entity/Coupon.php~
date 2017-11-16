<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;
/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_coupon")
 * @ORM\Entity(repositoryClass="LilWorks\StoreBundle\Entity\Repository\CouponRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Coupon
{

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $spent = 0;
        if( count($this->getOrdersPaymentMethods())>0 ){
            foreach($this->getOrdersPaymentMethods() as $orderPaymentMethod)
                $spent+=$orderPaymentMethod->getAmount();
        }

        if((!$this->splitable || $this->splitable==0) && count($this->getOrdersPaymentMethods())>0)
            $notValidForAmount = true;

        if($this->amount - $spent <= 0)
            $notValidForAmount = true;

        $now = new DateTime();
        if($this->validity && $now > $this->validity)
            $notValidForDate = true;

        if(isset($notValidForDate) || isset($notValidForAmount))
            $this->isActive = 0;
        else
            $this->isActive = 1;

    }

    public function  getAmountAvailable(){
        $spent = 0;
        foreach($this->getOrdersPaymentMethods() as $orderPaymentMethod){
            $spent+=$orderPaymentMethod->getAmount();
        }

        return $this->amount - $spent;
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Address")
     * @ORM\JoinColumn(name="address", referencedColumnName="id",onDelete="SET NULL")
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\DepositSalesPaymentMethods", mappedBy="coupon",cascade={"persist"})
     */
    private $depositSalesPaymentMethods;
    
    /**
     * One Coupon has Many OrderPaymentMethod.
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersPaymentMethods", mappedBy="coupon",cascade={"persist"})
     */
    private $ordersPaymentMethods;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\ReturnsPaymentMethods", mappedBy="coupon",cascade={"persist"})
     */
    private $returnsPaymentMethods;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Customer", inversedBy="coupons")
     * @ORM\JoinColumn(name="customer", referencedColumnName="id" , nullable=false)
     * @Assert\NotBlank()
     */
    protected $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string",length=100,nullable=false)
     */
    private $reference;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=false)
     * @Assert\GreaterThan(0)
     *
     */
    private $amount;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * @var text
     *
     * @ORM\Column(name="descriptionInternal", type="text",nullable=true)
     */
    private $descriptionInternal;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="createdAt", type="datetime",nullable=true)
     */
    private $createdAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="validity", type="datetime",nullable=true)
     */
    private $validity;

    /**
     * @var boolean
     *
     * @ORM\Column(name="splitable", type="boolean",nullable=true)
     */
    private $splitable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean",nullable=true)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="availableOnline", type="boolean",nullable=true)
     */
    private $availableOnline;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ordersPaymentMethods = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set reference
     *
     * @param string $reference
     *
     * @return Coupon
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Coupon
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
     * @return Coupon
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Coupon
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set validity
     *
     * @param \DateTime $validity
     *
     * @return Coupon
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Get validity
     *
     * @return \DateTime
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * Set splitable
     *
     * @param boolean $splitable
     *
     * @return Coupon
     */
    public function setSplitable($splitable)
    {
        $this->splitable = $splitable;

        return $this;
    }

    /**
     * Get splitable
     *
     * @return boolean
     */
    public function getSplitable()
    {
        return $this->splitable;
    }

    /**
     * Set availableOnline
     *
     * @param boolean $availableOnline
     *
     * @return Coupon
     */
    public function setAvailableOnline($availableOnline)
    {
        $this->availableOnline = $availableOnline;

        return $this;
    }

    /**
     * Get availableOnline
     *
     * @return boolean
     */
    public function getAvailableOnline()
    {
        return $this->availableOnline;
    }

    /**
     * Add ordersPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod
     *
     * @return Coupon
     */
    public function addOrdersPaymentMethod(\LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod)
    {
        $this->ordersPaymentMethods[] = $ordersPaymentMethod;

        return $this;
    }

    /**
     * Remove ordersPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod
     */
    public function removeOrdersPaymentMethod(\LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod)
    {
        $this->ordersPaymentMethods->removeElement($ordersPaymentMethod);
    }

    /**
     * Get ordersPaymentMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrdersPaymentMethods()
    {
        return $this->ordersPaymentMethods;
    }

    /**
     * Set customer
     *
     * @param \LilWorks\StoreBundle\Entity\Customer $customer
     *
     * @return Coupon
     */
    public function setCustomer(\LilWorks\StoreBundle\Entity\Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \LilWorks\StoreBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Coupon
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set address
     *
     * @param \LilWorks\StoreBundle\Entity\Address $address
     *
     * @return Coupon
     */
    public function setAddress(\LilWorks\StoreBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \LilWorks\StoreBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set descriptionInternal
     *
     * @param string $descriptionInternal
     *
     * @return Coupon
     */
    public function setDescriptionInternal($descriptionInternal)
    {
        $this->descriptionInternal = $descriptionInternal;

        return $this;
    }

    /**
     * Get descriptionInternal
     *
     * @return string
     */
    public function getDescriptionInternal()
    {
        return $this->descriptionInternal;
    }

    /**
     * Add returnsPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ReturnsPaymentMethods $returnsPaymentMethod
     *
     * @return Coupon
     */
    public function addReturnsPaymentMethod(\LilWorks\StoreBundle\Entity\ReturnsPaymentMethods $returnsPaymentMethod)
    {
        $this->returnsPaymentMethods[] = $returnsPaymentMethod;

        return $this;
    }

    /**
     * Remove returnsPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ReturnsPaymentMethods $returnsPaymentMethod
     */
    public function removeReturnsPaymentMethod(\LilWorks\StoreBundle\Entity\ReturnsPaymentMethods $returnsPaymentMethod)
    {
        $this->returnsPaymentMethods->removeElement($returnsPaymentMethod);
    }

    /**
     * Get returnsPaymentMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReturnsPaymentMethods()
    {
        return $this->returnsPaymentMethods;
    }

    /**
     * Add depositSalesPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\DepositSalesPaymentMethods $depositSalesPaymentMethod
     *
     * @return Coupon
     */
    public function addDepositSalesPaymentMethod(\LilWorks\StoreBundle\Entity\DepositSalesPaymentMethods $depositSalesPaymentMethod)
    {
        $this->depositSalesPaymentMethods[] = $depositSalesPaymentMethod;

        return $this;
    }

    /**
     * Remove depositSalesPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\DepositSalesPaymentMethods $depositSalesPaymentMethod
     */
    public function removeDepositSalesPaymentMethod(\LilWorks\StoreBundle\Entity\DepositSalesPaymentMethods $depositSalesPaymentMethod)
    {
        $this->depositSalesPaymentMethods->removeElement($depositSalesPaymentMethod);
    }

    /**
     * Get depositSalesPaymentMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepositSalesPaymentMethods()
    {
        return $this->depositSalesPaymentMethods;
    }
}
