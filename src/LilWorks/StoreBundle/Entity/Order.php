<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_order")
 * @ORM\Entity(repositoryClass="LilWorks\StoreBundle\Entity\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Order
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
        if(!$this->createdAt)
            $this->createdAt = new \DateTime();
        else
            $this->updatedAt= new \DateTime();

    }

    public function tot(){
        $tot=0;

        foreach($this->getOrdersProducts() as $orderProduct){
            if($orderProduct->getPrice()){
                $tot+=$orderProduct->getQuantity()*$orderProduct->getPrice();
            }else{
                $tot+=$orderProduct->getQuantity()*$orderProduct->getProduct()->getPriceOffline();
            }
        }
        if(count($this->getOrdersRealShippingMethods())>0){
            foreach($this->getOrdersRealShippingMethods() as $orderRealShippingMethod){
                $tot+=$orderRealShippingMethod->getPrice();
            }
        }

        return $tot;
    }
    public function payed(){
        $payed = 0;
        foreach($this->getOrdersPaymentMethods() as $paymentMethod){
            $payed+=$paymentMethod->getAmount();
        }

        return $payed;
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
     * @ORM\Column(name="reference", type="string",length=100,nullable=true)
     */
    private $reference;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\OrderType", inversedBy="orders" )
     * @ORM\JoinColumn(name="orderType", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $orderType;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdOrders" )
     * @ORM\JoinColumn(name="seller", referencedColumnName="id", nullable=true)
     */
    private $userAsSeller;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Customer", inversedBy="orders", cascade={"persist"})
     * @ORM\JoinColumn(name="customer", referencedColumnName="id" , nullable=true)
     */
    protected $customer;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersOrderSteps", mappedBy="order", cascade={"remove","persist"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $ordersOrderSteps;



    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersProducts", mappedBy="order", cascade={"remove","persist"})
     */
    private $ordersProducts;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersPaymentMethods", mappedBy="order" ,cascade={"remove","persist"})
     */
    private $ordersPaymentMethods;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersRealShippingMethods", mappedBy="order", cascade={"all"})
     */
    private $ordersRealShippingMethods;



    /**
     * @var datetime
     *
     * @ORM\Column(name="createdAt", type="datetime",nullable=true)
     */
    private $createdAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updatedAt", type="datetime",nullable=true)
     */
    private $updatedAt;

    /**
     * @var text
     *
     * @ORM\Column(name="userComment", type="text",nullable=true)
     */
    private $userComment;

    /**
     * @var text
     *
     * @ORM\Column(name="storeComment", type="text",nullable=true)
     */
    private $storeComment;


    /**
     * @var text
     *
     * @ORM\Column(name="shippingAddressString", type="text",nullable=true)
     */
    private $shippingAddressString;

    /**
     * @var text
     *
     * @ORM\Column(name="billingingAddressString", type="text",nullable=true)
     */
    private $billingAddressString;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Address")
     * @ORM\JoinColumn(name="billingAddress", referencedColumnName="id",onDelete="SET NULL")
     */
    private $billingAddress;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Address")
     * @ORM\JoinColumn(name="shippingAddress", referencedColumnName="id",onDelete="SET NULL")
     */
    private $shippingAddress;
    
    /**
     * @var float
     *
     * @ORM\Column(name="tot", type="float",nullable=true)
     */
    private $tot;

    /**
     * @var float
     *
     * @ORM\Column(name="payed", type="float",nullable=true)
     */
    private $payed;

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
     * Constructor
     */
    public function __construct()
    {
        if(!$this->createdAt)
            $this->createdAt = new \DateTime();
        $this->ordersOrderSteps = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ordersProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ordersPaymentMethods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ordersRealShippingMethods = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Order
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Order
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Order
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set userComment
     *
     * @param string $userComment
     *
     * @return Order
     */
    public function setUserComment($userComment)
    {
        $this->userComment = $userComment;

        return $this;
    }

    /**
     * Get userComment
     *
     * @return string
     */
    public function getUserComment()
    {
        return $this->userComment;
    }

    /**
     * Set storeComment
     *
     * @param string $storeComment
     *
     * @return Order
     */
    public function setStoreComment($storeComment)
    {
        $this->storeComment = $storeComment;

        return $this;
    }

    /**
     * Get storeComment
     *
     * @return string
     */
    public function getStoreComment()
    {
        return $this->storeComment;
    }

    /**
     * Set shippingAddressString
     *
     * @param string $shippingAddressString
     *
     * @return Order
     */
    public function setShippingAddressString($shippingAddressString)
    {
        $this->shippingAddressString = $shippingAddressString;

        return $this;
    }

    /**
     * Get shippingAddressString
     *
     * @return string
     */
    public function getShippingAddressString()
    {
        return $this->shippingAddressString;
    }

    /**
     * Set billingAddressString
     *
     * @param string $billingAddressString
     *
     * @return Order
     */
    public function setBillingAddressString($billingAddressString)
    {
        $this->billingAddressString = $billingAddressString;

        return $this;
    }

    /**
     * Get billingAddressString
     *
     * @return string
     */
    public function getBillingAddressString()
    {
        return $this->billingAddressString;
    }

    /**
     * Set tot
     *
     * @param float $tot
     *
     * @return Order
     */
    public function setTot($tot)
    {
        $this->tot = $tot;

        return $this;
    }

    /**
     * Get tot
     *
     * @return float
     */
    public function getTot()
    {
        return $this->tot;
    }

    /**
     * Set payed
     *
     * @param float $payed
     *
     * @return Order
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;

        return $this;
    }

    /**
     * Get payed
     *
     * @return float
     */
    public function getPayed()
    {
        return $this->payed;
    }

    /**
     * Set orderType
     *
     * @param \LilWorks\StoreBundle\Entity\OrderType $orderType
     *
     * @return Order
     */
    public function setOrderType(\LilWorks\StoreBundle\Entity\OrderType $orderType = null)
    {
        $this->orderType = $orderType;

        return $this;
    }

    /**
     * Get orderType
     *
     * @return \LilWorks\StoreBundle\Entity\OrderType
     */
    public function getOrderType()
    {
        return $this->orderType;
    }

    /**
     * Set userAsSeller
     *
     * @param \AppBundle\Entity\User $userAsSeller
     *
     * @return Order
     */
    public function setUserAsSeller(\AppBundle\Entity\User $userAsSeller = null)
    {
        $this->userAsSeller = $userAsSeller;

        return $this;
    }

    /**
     * Get userAsSeller
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserAsSeller()
    {
        return $this->userAsSeller;
    }

    /**
     * Set customer
     *
     * @param \LilWorks\StoreBundle\Entity\Customer $customer
     *
     * @return Order
     */
    public function setCustomer(\LilWorks\StoreBundle\Entity\Customer $customer = null)
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
     * Add ordersOrderStep
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersOrderSteps $ordersOrderStep
     *
     * @return Order
     */
    public function addOrdersOrderStep(\LilWorks\StoreBundle\Entity\OrdersOrderSteps $ordersOrderStep)
    {
        $this->ordersOrderSteps[] = $ordersOrderStep;

        return $this;
    }

    /**
     * Remove ordersOrderStep
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersOrderSteps $ordersOrderStep
     */
    public function removeOrdersOrderStep(\LilWorks\StoreBundle\Entity\OrdersOrderSteps $ordersOrderStep)
    {
        $this->ordersOrderSteps->removeElement($ordersOrderStep);
    }

    /**
     * Get ordersOrderSteps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrdersOrderSteps()
    {
        return $this->ordersOrderSteps;
    }

    /**
     * Add ordersProduct
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersProducts $ordersProduct
     *
     * @return Order
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
     * Add ordersPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod
     *
     * @return Order
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
     * Add ordersRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $ordersRealShippingMethod
     *
     * @return Order
     */
    public function addOrdersRealShippingMethod(\LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $ordersRealShippingMethod)
    {
        $this->ordersRealShippingMethods[] = $ordersRealShippingMethod;

        return $this;
    }

    /**
     * Remove ordersRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $ordersRealShippingMethod
     */
    public function removeOrdersRealShippingMethod(\LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $ordersRealShippingMethod)
    {
        $this->ordersRealShippingMethods->removeElement($ordersRealShippingMethod);
    }

    /**
     * Get ordersRealShippingMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrdersRealShippingMethods()
    {
        return $this->ordersRealShippingMethods;
    }

    /**
     * Set billingAddress
     *
     * @param \LilWorks\StoreBundle\Entity\Address $billingAddress
     *
     * @return Order
     */
    public function setBillingAddress(\LilWorks\StoreBundle\Entity\Address $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \LilWorks\StoreBundle\Entity\Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set shippingAddress
     *
     * @param \LilWorks\StoreBundle\Entity\Address $shippingAddress
     *
     * @return Order
     */
    public function setShippingAddress(\LilWorks\StoreBundle\Entity\Address $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return \LilWorks\StoreBundle\Entity\Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Order
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
     * Set descriptionInternal
     *
     * @param string $descriptionInternal
     *
     * @return Order
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
}
