<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_basket")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="LilWorks\StoreBundle\Entity\Repository\BasketRepository")
 */
class Basket
{
    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s')));

        if($this->getCreatedAt() == null)
        {
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"toOrder"})
     * @MaxDepth(1)
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="baskets")
     * @ORM\JoinColumn(name="user", referencedColumnName="id" , nullable=true)
     * @Groups({"toOrder"})
     * @MaxDepth(1)
     */
    protected $user;


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Session", inversedBy="basket")
     * @ORM\JoinColumn(name="token", referencedColumnName="sess_id", nullable = true)
     */
    private $token;

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
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\BasketsProducts", mappedBy="basket", cascade={"remove","persist"})
     * @Groups({"toOrder"})
     * @MaxDepth(2)
     */
    private $basketsProducts;



    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\BasketsRealShippingMethods", mappedBy="basket", cascade={"remove","persist"})
     * @Groups({"toOrder"})
     * @MaxDepth(5)
     */
    private $basketsRealShippingMethods;


    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Address")
     * @ORM\JoinColumn(name="billingAddress", referencedColumnName="id",onDelete="SET NULL")
     *
     * @MaxDepth(1)
     */
    private $billingAddress;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Address")
     * @ORM\JoinColumn(name="shippingAddress", referencedColumnName="id",onDelete="SET NULL")
     * @MaxDepth(1)
     */
    private $shippingAddress;
    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(name="paymentMethod", referencedColumnName="id")
     * @MaxDepth(1)
     */
    private $paymentMethod;

    /**
     * @var float
     *
     * @ORM\Column(type="float",name="tot",nullable=true)
     */
    private $tot;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->basketsProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->basketsRealShippingMethods = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Basket
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
     * @return Basket
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
     * Set tot
     *
     * @param float $tot
     *
     * @return Basket
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Basket
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set token
     *
     * @param \AppBundle\Entity\Session $token
     *
     * @return Basket
     */
    public function setToken(\AppBundle\Entity\Session $token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return \AppBundle\Entity\Session
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Add basketsProduct
     *
     * @param \LilWorks\StoreBundle\Entity\BasketsProducts $basketsProduct
     *
     * @return Basket
     */
    public function addBasketsProduct(\LilWorks\StoreBundle\Entity\BasketsProducts $basketsProduct)
    {
        $this->basketsProducts[] = $basketsProduct;

        return $this;
    }

    /**
     * Remove basketsProduct
     *
     * @param \LilWorks\StoreBundle\Entity\BasketsProducts $basketsProduct
     */
    public function removeBasketsProduct(\LilWorks\StoreBundle\Entity\BasketsProducts $basketsProduct)
    {
        $this->basketsProducts->removeElement($basketsProduct);
    }

    /**
     * Get basketsProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBasketsProducts()
    {
        return $this->basketsProducts;
    }

    /**
     * Add basketsRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\BasketsRealShippingMethods $basketsRealShippingMethod
     *
     * @return Basket
     */
    public function addBasketsRealShippingMethod(\LilWorks\StoreBundle\Entity\BasketsRealShippingMethods $basketsRealShippingMethod)
    {
        $this->basketsRealShippingMethods[] = $basketsRealShippingMethod;

        return $this;
    }

    /**
     * Remove basketsRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\BasketsRealShippingMethods $basketsRealShippingMethod
     */
    public function removeBasketsRealShippingMethod(\LilWorks\StoreBundle\Entity\BasketsRealShippingMethods $basketsRealShippingMethod)
    {
        $this->basketsRealShippingMethods->removeElement($basketsRealShippingMethod);
    }

    /**
     * Get basketsRealShippingMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBasketsRealShippingMethods()
    {
        return $this->basketsRealShippingMethods;
    }

    /**
     * Set billingAddress
     *
     * @param \LilWorks\StoreBundle\Entity\Address $billingAddress
     *
     * @return Basket
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
     * @return Basket
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
     * Set paymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return Basket
     */
    public function setPaymentMethod(\LilWorks\StoreBundle\Entity\PaymentMethod $paymentMethod = null)
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
