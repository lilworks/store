<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_orders_products")
 * @ORM\HasLifecycleCallbacks()
 */
class OrdersProducts
{

    public function __clone(){
        if($this->id){
            $this->id = null;
        }
    }
    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {

    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Tax", inversedBy="ordersProducts" )
     * @ORM\JoinTable(name="lilworks_ordersProducts_taxes")
     */
    private $taxes;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\OrdersRealShippingMethods", inversedBy="ordersProducts", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="orderRealShippingMethod", referencedColumnName="id" , nullable=true)
     */
    protected $orderRealShippingMethod;



    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Order", inversedBy="ordersProducts",cascade={"persist"})
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Product", inversedBy="ordersProducts",cascade={"persist"})
     * @ORM\JoinColumn(name="product", referencedColumnName="id", nullable=true)
     */
    protected $product;

    /**
     * Many Products have Many Warranties.
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Warranty", inversedBy="ordersProducts")
     * @ORM\JoinTable(name="lilworks_order_products_warranties")
     */
    private $warranties;



    /**
     * @var string
     *
     * @ORM\Column(name="warranty_string", type="string",length=255,nullable=true)
     */
    private $warrantieString;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255,nullable=true)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float",nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="integer",nullable=true)
     * @Assert\GreaterThan(0)
     */
    private $quantity;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="serialNumber", type="string",length=255,nullable=true)
     */
    private $serialNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSecondHand", type="boolean",nullable=true)
     */
    private $isSecondHand;

    /**
     * @var boolean
     *
     * @ORM\Column(name="destocking", type="boolean",nullable=true)
     */
    private $destocking;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taxes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->warranties = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set warrantieString
     *
     * @param string $warrantieString
     *
     * @return OrdersProducts
     */
    public function setWarrantieString($warrantieString)
    {
        $this->warrantieString = $warrantieString;

        return $this;
    }

    /**
     * Get warrantieString
     *
     * @return string
     */
    public function getWarrantieString()
    {
        return $this->warrantieString;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return OrdersProducts
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return OrdersProducts
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrdersProducts
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
     * @return OrdersProducts
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
     * Set serialNumber
     *
     * @param string $serialNumber
     *
     * @return OrdersProducts
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get serialNumber
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set isSecondHand
     *
     * @param boolean $isSecondHand
     *
     * @return OrdersProducts
     */
    public function setIsSecondHand($isSecondHand)
    {
        $this->isSecondHand = $isSecondHand;

        return $this;
    }

    /**
     * Get isSecondHand
     *
     * @return boolean
     */
    public function getIsSecondHand()
    {
        return $this->isSecondHand;
    }

    /**
     * Add tax
     *
     * @param \LilWorks\StoreBundle\Entity\Tax $tax
     *
     * @return OrdersProducts
     */
    public function addTax(\LilWorks\StoreBundle\Entity\Tax $tax)
    {
        $this->taxes[] = $tax;

        return $this;
    }

    /**
     * Remove tax
     *
     * @param \LilWorks\StoreBundle\Entity\Tax $tax
     */
    public function removeTax(\LilWorks\StoreBundle\Entity\Tax $tax)
    {
        $this->taxes->removeElement($tax);
    }

    /**
     * Get taxes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * Set orderRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $orderRealShippingMethod
     *
     * @return OrdersProducts
     */
    public function setOrderRealShippingMethod(\LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $orderRealShippingMethod = null)
    {
        $this->orderRealShippingMethod = $orderRealShippingMethod;

        return $this;
    }

    /**
     * Get orderRealShippingMethod
     *
     * @return \LilWorks\StoreBundle\Entity\OrdersRealShippingMethods
     */
    public function getOrderRealShippingMethod()
    {
        return $this->orderRealShippingMethod;
    }

    /**
     * Set order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     *
     * @return OrdersProducts
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
     * Set product
     *
     * @param \LilWorks\StoreBundle\Entity\Product $product
     *
     * @return OrdersProducts
     */
    public function setProduct(\LilWorks\StoreBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \LilWorks\StoreBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Add warranty
     *
     * @param \LilWorks\StoreBundle\Entity\Warranty $warranty
     *
     * @return OrdersProducts
     */
    public function addWarranty(\LilWorks\StoreBundle\Entity\Warranty $warranty)
    {
        $this->warranties[] = $warranty;

        return $this;
    }

    /**
     * Remove warranty
     *
     * @param \LilWorks\StoreBundle\Entity\Warranty $warranty
     */
    public function removeWarranty(\LilWorks\StoreBundle\Entity\Warranty $warranty)
    {
        $this->warranties->removeElement($warranty);
    }

    /**
     * Get warranties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWarranties()
    {
        return $this->warranties;
    }

    /**
     * Set destocking
     *
     * @param boolean $destocking
     *
     * @return OrdersProducts
     */
    public function setDestocking($destocking)
    {
        $this->destocking = $destocking;

        return $this;
    }

    /**
     * Get destocking
     *
     * @return boolean
     */
    public function getDestocking()
    {
        return $this->destocking;
    }
}
