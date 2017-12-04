<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_baskets_realshippingmethods")
 */
class BasketsRealShippingMethods
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
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Basket", inversedBy="basketsRealShippingMethods")
     * @ORM\JoinColumn(name="basket", referencedColumnName="id", nullable=true)
     */
    protected $basket;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethod", inversedBy="basketsRealShippingMethods")
     * @ORM\JoinColumn(name="shippingMethod", referencedColumnName="id" , nullable=true)
     */
    protected $shippingMethod;


    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\BasketsProducts", mappedBy="basketRealShippingMethod", cascade={"all"})
     */
    private $basketsProducts;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->basketsProducts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return BasketsRealShippingMethods
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
     * @return BasketsRealShippingMethods
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
     * Set basket
     *
     * @param \LilWorks\StoreBundle\Entity\Basket $basket
     *
     * @return BasketsRealShippingMethods
     */
    public function setBasket(\LilWorks\StoreBundle\Entity\Basket $basket = null)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get basket
     *
     * @return \LilWorks\StoreBundle\Entity\Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Set shippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod
     *
     * @return BasketsRealShippingMethods
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
     * Add basketsProduct
     *
     * @param \LilWorks\StoreBundle\Entity\BasketsProducts $basketsProduct
     *
     * @return BasketsRealShippingMethods
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
}
