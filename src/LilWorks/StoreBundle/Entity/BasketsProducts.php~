<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_baskets_products")
 */
class BasketsProducts
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer" , nullable=true)
     * @Assert\LessThan(1000)
     */
    private $quantity;



    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Product", inversedBy="basketsProducts")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Basket", inversedBy="basketsProducts")
     * @ORM\JoinColumn(name="basket", referencedColumnName="id", nullable=true)
     */
    protected $basket;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\BasketsRealShippingMethods", inversedBy="basketsProducts")
     * @ORM\JoinColumn(name="basketRealShippingMethod", referencedColumnName="id" , nullable=true )
     */
    protected $basketRealShippingMethod;



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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return BasketsProducts
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
     * Set product
     *
     * @param \LilWorks\StoreBundle\Entity\Product $product
     *
     * @return BasketsProducts
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
     * Set basket
     *
     * @param \LilWorks\StoreBundle\Entity\Basket $basket
     *
     * @return BasketsProducts
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
     * Set basketRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\BasketsRealShippingMethods $basketRealShippingMethod
     *
     * @return BasketsProducts
     */
    public function setBasketRealShippingMethod(\LilWorks\StoreBundle\Entity\BasketsRealShippingMethods $basketRealShippingMethod = null)
    {
        $this->basketRealShippingMethod = $basketRealShippingMethod;

        return $this;
    }

    /**
     * Get basketRealShippingMethod
     *
     * @return \LilWorks\StoreBundle\Entity\BasketsRealShippingMethods
     */
    public function getBasketRealShippingMethod()
    {
        return $this->basketRealShippingMethod;
    }
}
