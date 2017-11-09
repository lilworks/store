<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_shipping_methods_triggers")
 */
class ShippingMethodsTriggers
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethod", inversedBy="triggers",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="shippingMethod", referencedColumnName="id")
     */
    private $shippingMethod;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float",nullable=false)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="shippingMethodTrigger", type="float",nullable=false)
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
     * @return ShippingMethodsTriggers
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
     * @return ShippingMethodsTriggers
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
     * Set shippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod
     *
     * @return ShippingMethodsTriggers
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
}
