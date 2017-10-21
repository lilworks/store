<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_shipping_methods_countries")
 */
class ShippingMethodsCountries
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethod", inversedBy="shippingmethods_countries")
     * @ORM\JoinColumn(name="shippingMethod", referencedColumnName="id", nullable=FALSE)
     * @Assert\NotBlank()
     */
    protected $shippingMethod;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Country", inversedBy="shippingmethods_countries")
     * @ORM\JoinColumn(name="country", referencedColumnName="id", nullable=FALSE)
     */
    protected $country;


    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float",nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="freeTrigger", type="float",nullable=true)
     */
    private $freeTrigger;



    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean",nullable=true)
     */
    private $isPublished;


    /**
     * @var integer
     *
     * @ORM\Column(name="delay", type="integer",nullable=true)
     */
    private $delay;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer",nullable=true)
     */
    private $priority;



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
     * @return ShippingMethodsCountries
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
     * Set freeTrigger
     *
     * @param float $freeTrigger
     *
     * @return ShippingMethodsCountries
     */
    public function setFreeTrigger($freeTrigger)
    {
        $this->freeTrigger = $freeTrigger;

        return $this;
    }

    /**
     * Get freeTrigger
     *
     * @return float
     */
    public function getFreeTrigger()
    {
        return $this->freeTrigger;
    }

    /**
     * Set shippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod
     *
     * @return ShippingMethodsCountries
     */
    public function setShippingMethod(\LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod)
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
     * Set country
     *
     * @param \LilWorks\StoreBundle\Entity\Country $country
     *
     * @return ShippingMethodsCountries
     */
    public function setCountry(\LilWorks\StoreBundle\Entity\Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \LilWorks\StoreBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return ShippingMethodsCountries
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set delay
     *
     * @param integer $delay
     *
     * @return ShippingMethodsCountries
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Get delay
     *
     * @return integer
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return ShippingMethodsCountries
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
