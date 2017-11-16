<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_tax")
 */
class Tax
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="taxesOffline")
     */
    private $productsOffline;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="taxesOnline")
     */
    private $productsOnline;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersProducts", mappedBy="taxes")
     */
    private $ordersProducts;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255,nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float",nullable=false)
     * @Assert\NotBlank()
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string",length=10,nullable=false)
     * @Assert\NotBlank()
     */
    private $type;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productsOffline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productsOnline = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Tax
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
     * Set value
     *
     * @param float $value
     *
     * @return Tax
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Tax
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add productsOffline
     *
     * @param \LilWorks\StoreBundle\Entity\Product $productsOffline
     *
     * @return Tax
     */
    public function addProductsOffline(\LilWorks\StoreBundle\Entity\Product $productsOffline)
    {
        $this->productsOffline[] = $productsOffline;

        return $this;
    }

    /**
     * Remove productsOffline
     *
     * @param \LilWorks\StoreBundle\Entity\Product $productsOffline
     */
    public function removeProductsOffline(\LilWorks\StoreBundle\Entity\Product $productsOffline)
    {
        $this->productsOffline->removeElement($productsOffline);
    }

    /**
     * Get productsOffline
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductsOffline()
    {
        return $this->productsOffline;
    }

    /**
     * Add productsOnline
     *
     * @param \LilWorks\StoreBundle\Entity\Product $productsOnline
     *
     * @return Tax
     */
    public function addProductsOnline(\LilWorks\StoreBundle\Entity\Product $productsOnline)
    {
        $this->productsOnline[] = $productsOnline;

        return $this;
    }

    /**
     * Remove productsOnline
     *
     * @param \LilWorks\StoreBundle\Entity\Product $productsOnline
     */
    public function removeProductsOnline(\LilWorks\StoreBundle\Entity\Product $productsOnline)
    {
        $this->productsOnline->removeElement($productsOnline);
    }

    /**
     * Get productsOnline
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductsOnline()
    {
        return $this->productsOnline;
    }

    /**
     * Add ordersProduct
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersProducts $ordersProduct
     *
     * @return Tax
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
}
