<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_order_product_return")
 * @ORM\Entity(repositoryClass="LilWorks\StoreBundle\Entity\Repository\OrderProductReturnRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class OrderProductReturn
{

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if(!$this->returnedAt)
            $this->returnedAt = new \DateTime();

    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string",length=20,nullable=false)
     */
    private $reference;


    /**
     * @var datetime
     *
     * @ORM\Column(name="returnedAt", type="datetime",nullable=false)
     */
    private $returnedAt;


    /**
     * @ORM\OneToOne(targetEntity="LilWorks\StoreBundle\Entity\OrdersProducts", inversedBy="orderProductReturn")
     * @ORM\JoinColumn(name="orderProduct", referencedColumnName="id")
     */
    private $orderProduct;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\ReturnsPaymentMethods", mappedBy="orderProductReturn" ,cascade={"remove","persist"})
     * @Assert\Valid()
     */
    private $returnsPaymentMethods;


    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethod")
     * @ORM\JoinColumn(name="shippingMethod", referencedColumnName="id",nullable=true)
     */
    private $shippingMethod;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isArchived", type="boolean",nullable=true)
     */
    private $isArchived;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer",nullable=false)
     */
    private $quantity;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->returnsPaymentMethods = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set returnedAt
     *
     * @param \DateTime $returnedAt
     *
     * @return OrderProductReturn
     */
    public function setReturnedAt($returnedAt)
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    /**
     * Get returnedAt
     *
     * @return \DateTime
     */
    public function getReturnedAt()
    {
        return $this->returnedAt;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderProductReturn
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return OrderProductReturn
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
     * Set orderProduct
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersProducts $orderProduct
     *
     * @return OrderProductReturn
     */
    public function setOrderProduct(\LilWorks\StoreBundle\Entity\OrdersProducts $orderProduct = null)
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }

    /**
     * Get orderProduct
     *
     * @return \LilWorks\StoreBundle\Entity\OrdersProducts
     */
    public function getOrderProduct()
    {
        return $this->orderProduct;
    }

    /**
     * Add returnsPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ReturnsPaymentMethods $returnsPaymentMethod
     *
     * @return OrderProductReturn
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
     * Set shippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod
     *
     * @return OrderProductReturn
     */
    public function setShippingMethod(\LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod = null)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * Get shippingMethod
     *
     * @return \LilWorks\StoreBundle\Entity\ShippingMethod
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return OrderProductReturn
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
     * Set isArchived
     *
     * @param boolean $isArchived
     *
     * @return OrderProductReturn
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }
}
