<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_shipping_methods_countries_triggers")
 * @ORM\Entity(repositoryClass="LilWorks\StoreBundle\Entity\Repository\ShippingMethodsCountriesTriggersRepository")
 */
class ShippingMethodsCountriesTriggers
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethodsCountries", inversedBy="triggers",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="shippingMethodCountry", referencedColumnName="id")
     */
    private $shippingMethodCountry;



    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float",nullable=false)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="shippingMethodCountryTrigger", type="float",nullable=false)
     */
    private $trigger;


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
     * @return ShippingMethodsCountriesTriggers
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
     * Set trigger
     *
     * @param float $trigger
     *
     * @return ShippingMethodsCountriesTriggers
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * Get trigger
     *
     * @return float
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * Set shippingMethodCountry
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingMethodCountry
     *
     * @return ShippingMethodsCountriesTriggers
     */
    public function setShippingMethodCountry(\LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingMethodCountry = null)
    {
        $this->shippingMethodCountry = $shippingMethodCountry;

        return $this;
    }

    /**
     * Get shippingMethodCountry
     *
     * @return \LilWorks\StoreBundle\Entity\ShippingMethodsCountries
     */
    public function getShippingMethodCountry()
    {
        return $this->shippingMethodCountry;
    }
}
