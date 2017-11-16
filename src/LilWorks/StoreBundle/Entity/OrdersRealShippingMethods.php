<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_orders_realshippingmethods")
 */
class OrdersRealShippingMethods
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float" , nullable=true)
     */
    private $price;


    /**
     * @var text
     *
     * @ORM\Column(name="reference", type="text" , nullable=true)
     */
    private $reference;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Order", inversedBy="ordersRealShippingMethods")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=true)
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethod", inversedBy="ordersRealShippingMethods")
     * @ORM\JoinColumn(name="shippingMethod", referencedColumnName="id" , nullable=true)
     */
    protected $shippingMethod;


    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersProducts", mappedBy="orderRealShippingMethod", cascade={"persist"})
     */
    private $ordersProducts;


    /**
     * @var datetime
     *
     * @ORM\Column(name="shippedAt", type="datetime",nullable=true)
     */
    private $shippedAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="receivedAt", type="datetime",nullable=true)
     */
    private $receivedAt;

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
        $this->ordersProducts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set price
     *
     * @param float $price
     *
     * @return OrdersRealShippingMethods
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return OrdersRealShippingMethods
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
     * Set order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     *
     * @return OrdersRealShippingMethods
     */
    public function setOrder(\LilWorks\StoreBundle\Entity\Order $order = null)
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
     * Set shippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod
     *
     * @return OrdersRealShippingMethods
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
     * Add ordersProduct
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersProducts $ordersProduct
     *
     * @return OrdersRealShippingMethods
     */
    public function addOrdersProduct(\LilWorks\StoreBundle\Entity\OrdersProducts $ordersProduct)
    {
        $this->ordersProducts[] = $ordersProduct;

        return $this;
    }

    /**
     * Remove ordersProduct
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersProducts $ordersProduct
     */
    public function removeOrdersProduct(\LilWorks\StoreBundle\Entity\OrdersProducts $ordersProduct)
    {
        $this->ordersProducts->removeElement($ordersProduct);
    }

    /**
     * Get ordersProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrdersProducts()
    {
        return $this->ordersProducts;
    }


    /**
     * Set shippedAt
     *
     * @param \DateTime $shippedAt
     *
     * @return OrdersRealShippingMethods
     */
    public function setShippedAt($shippedAt)
    {
        $this->shippedAt = $shippedAt;

        return $this;
    }

    /**
     * Get shippedAt
     *
     * @return \DateTime
     */
    public function getShippedAt()
    {
        return $this->shippedAt;
    }

    /**
     * Set receivedAt
     *
     * @param \DateTime $receivedAt
     *
     * @return OrdersRealShippingMethods
     */
    public function setReceivedAt($receivedAt)
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }

    /**
     * Get receivedAt
     *
     * @return \DateTime
     */
    public function getReceivedAt()
    {
        return $this->receivedAt;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return OrdersRealShippingMethods
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
}
