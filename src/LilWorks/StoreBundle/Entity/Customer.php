<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_customer")
 * @ORM\HasLifecycleCallbacks
 */
class Customer
{

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        if($this->getCreatedAt() == null)
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var datetime
     *
     * @ORM\Column(name="createdAt", type="datetime",nullable=true)
     */
    private $createdAt;

    /**
     * One Customer is One User.
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="customer" , cascade={"detach"})
     * @ORM\JoinColumn(name="user", referencedColumnName="id",nullable=true)
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="remoteUser", type="integer",length=255,nullable=true)
     */
    private $remoteUser;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Order", mappedBy="customer",cascade={"persist"})
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\PhoneNumber", mappedBy="customer", cascade={"remove","persist"})
     */
    private $phonenumbers;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Address", mappedBy="customer", cascade={"remove","persist"})
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Coupon", mappedBy="customer", cascade={"remove","persist"})
     */
    private $coupons;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\DepositSale", mappedBy="customer", cascade={"remove","persist"})
     */
    private $depositSales;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string",length=255,nullable=true)
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string",length=255,nullable=true)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string",length=255,nullable=true)
     */
    private $companyName;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string",length=255,nullable=true)
     * @Assert\NotBlank()
     */
    private $email;
    /**
     * Constructor
     */
    public function __construct()
    {
        if(!$this->createdAt)
            $this->createdAt = new \DateTime();
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phonenumbers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->addresses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set remoteUser
     *
     * @param integer $remoteUser
     *
     * @return Customer
     */
    public function setRemoteUser($remoteUser)
    {
        $this->remoteUser = $remoteUser;

        return $this;
    }

    /**
     * Get remoteUser
     *
     * @return integer
     */
    public function getRemoteUser()
    {
        return $this->remoteUser;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Customer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Customer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Customer
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
     * Add order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     *
     * @return Customer
     */
    public function addOrder(\LilWorks\StoreBundle\Entity\Order $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     */
    public function removeOrder(\LilWorks\StoreBundle\Entity\Order $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Add phonenumber
     *
     * @param \LilWorks\StoreBundle\Entity\PhoneNumber $phonenumber
     *
     * @return Customer
     */
    public function addPhonenumber(\LilWorks\StoreBundle\Entity\PhoneNumber $phonenumber)
    {
        $this->phonenumbers[] = $phonenumber;

        return $this;
    }

    /**
     * Remove phonenumber
     *
     * @param \LilWorks\StoreBundle\Entity\PhoneNumber $phonenumber
     */
    public function removePhonenumber(\LilWorks\StoreBundle\Entity\PhoneNumber $phonenumber)
    {
        $this->phonenumbers->removeElement($phonenumber);
    }

    /**
     * Get phonenumbers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhonenumbers()
    {
        return $this->phonenumbers;
    }

    /**
     * Add address
     *
     * @param \LilWorks\StoreBundle\Entity\Address $address
     *
     * @return Customer
     */
    public function addAddress(\LilWorks\StoreBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \LilWorks\StoreBundle\Entity\Address $address
     */
    public function removeAddress(\LilWorks\StoreBundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return Customer
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Customer
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
     * Set description
     *
     * @param string $description
     *
     * @return Customer
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
     * Add coupon
     *
     * @param \LilWorks\StoreBundle\Entity\Coupon $coupon
     *
     * @return Customer
     */
    public function addCoupon(\LilWorks\StoreBundle\Entity\Coupon $coupon)
    {
        $this->coupons[] = $coupon;

        return $this;
    }

    /**
     * Remove coupon
     *
     * @param \LilWorks\StoreBundle\Entity\Coupon $coupon
     */
    public function removeCoupon(\LilWorks\StoreBundle\Entity\Coupon $coupon)
    {
        $this->coupons->removeElement($coupon);
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * Add depositSale
     *
     * @param \LilWorks\StoreBundle\Entity\DepositSale $depositSale
     *
     * @return Customer
     */
    public function addDepositSale(\LilWorks\StoreBundle\Entity\DepositSale $depositSale)
    {
        $this->depositSales[] = $depositSale;

        return $this;
    }

    /**
     * Remove depositSale
     *
     * @param \LilWorks\StoreBundle\Entity\DepositSale $depositSale
     */
    public function removeDepositSale(\LilWorks\StoreBundle\Entity\DepositSale $depositSale)
    {
        $this->depositSales->removeElement($depositSale);
    }

    /**
     * Get depositSales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepositSales()
    {
        return $this->depositSales;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
