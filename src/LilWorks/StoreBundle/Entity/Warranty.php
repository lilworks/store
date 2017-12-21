<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LilWorks\StoreBundle\Util\TagSanitizer;
/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_warranty")
 * @ORM\HasLifecycleCallbacks()
 */
class Warranty
{
    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function formatTag()
    {
        #if($this->tag == "" ){
            $tagSanitizer = new TagSanitizer();
            $this->setTag( $tagSanitizer->sanitize($this->name) );
        #}
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;



    /**
     * Many Warranties have Many OrdersProducts.
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\ordersProducts", mappedBy="warranties")
     */
    private $ordersProducts;





    /**
     * Many Warranties have Many Products.
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="warrantiesOffline")
     */
    private $productsOffline;

    /**
     * Many Warranties have Many Products.
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="warrantiesOnline")
     */
    private $productsOnline;




    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=50,nullable=false)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=50,nullable=false)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptionPublic", type="text",length=255,nullable=true)
     */
    private $descriptionPublic;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptionInternal", type="text",length=255,nullable=true)
     */
    private $descriptionInternal;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ordersProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productsOffline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productsOnline = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Warranty
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Warranty
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set descriptionPublic
     *
     * @param string $descriptionPublic
     *
     * @return Warranty
     */
    public function setDescriptionPublic($descriptionPublic)
    {
        $this->descriptionPublic = $descriptionPublic;

        return $this;
    }

    /**
     * Get descriptionPublic
     *
     * @return string
     */
    public function getDescriptionPublic()
    {
        return $this->descriptionPublic;
    }

    /**
     * Set descriptionInternal
     *
     * @param string $descriptionInternal
     *
     * @return Warranty
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

    /**
     * Add ordersProduct
     *
     * @param \LilWorks\StoreBundle\Entity\ordersProducts $ordersProduct
     *
     * @return Warranty
     */
    public function addOrdersProduct(\LilWorks\StoreBundle\Entity\ordersProducts $ordersProduct)
    {
        $this->ordersProducts[] = $ordersProduct;

        return $this;
    }

    /**
     * Remove ordersProduct
     *
     * @param \LilWorks\StoreBundle\Entity\ordersProducts $ordersProduct
     */
    public function removeOrdersProduct(\LilWorks\StoreBundle\Entity\ordersProducts $ordersProduct)
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
     * Add productsOffline
     *
     * @param \LilWorks\StoreBundle\Entity\Product $productsOffline
     *
     * @return Warranty
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
     * @return Warranty
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
}
