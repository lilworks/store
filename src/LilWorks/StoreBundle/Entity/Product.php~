<?php
namespace LilWorks\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LilWorks\StoreBundle\Util\TagSanitizer;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_product")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="LilWorks\StoreBundle\Entity\Repository\ProductRepository")
 */
class Product
{

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $sanitizer = new TagSanitizer();
        $this->tag = $sanitizer->sanitize($this->getName());
        if(!$this->priceOnline)
            $this->priceOnline = $this->priceOffline;

        if($this->stock < 0)
            $this->stock = 0;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\OneToOne(targetEntity="LilWorks\StoreBundle\Entity\DepositSale", mappedBy="product")
     */
    private $depositSale;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Category", inversedBy="products",cascade={"remove","persist"} )
     * @ORM\JoinTable(name="lilworks_products_categories")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Tag", inversedBy="products",cascade={"persist"} )
     * @ORM\JoinTable(name="lilworks_products_tags")
     * @ORM\OrderBy({"tag" = "ASC"})
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Brand", inversedBy="products" )
     * @ORM\JoinColumn(name="brand", referencedColumnName="id",nullable=true)
     */
    private $brand;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersProducts", mappedBy="product" )
     */
    private $ordersProducts;

    /**
     * Many Products have Many ShippingMethod.
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethod", inversedBy="products")
     * @ORM\JoinTable(name="lilworks_products_shippingMethods")
     */
    private $shippingMethods;

    /**
     * Many Products have Many Warranties.
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Warranty", inversedBy="productsOffline")
     * @ORM\JoinTable(name="lilworks_products_warranties_offline")
     */
    private $warrantiesOffline;

    /**
     * Many Products have Many Warranties.
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Warranty", inversedBy="productsOnline")
     * @ORM\JoinTable(name="lilworks_products_warranties_online")
     */
    private $warrantiesOnline;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="relatedProducts")
     */
    private $productsRelated;
    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", inversedBy="productsRelated")
     * @ORM\JoinTable(name="lilworks_products_related_products",
     *      joinColumns={@ORM\JoinColumn(name="product", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="related_product", referencedColumnName="id")}
     *      )
     */
    private $relatedProducts;


    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\BasketsProducts", mappedBy="product" ,cascade={"remove","persist"})
     */
    private $basketsProducts;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Picture", mappedBy="product", cascade={"persist", "remove"})
     * @ORM\OrderBy({"pos" = "ASC"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Review", mappedBy="product")
     */
    private $reviews;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=100,nullable=false)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255,nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean",nullable=true)
     */
    private $isPublished;

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
     * @var float
     *
     * @ORM\Column(name="priceOnline", type="float",nullable=true)
     */
    private $priceOnline;


    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Tax", inversedBy="productsOnline" , cascade={"persist"})
     * @ORM\JoinTable(name="lilworks_products_taxesOnline")
     */
    private $taxesOnline;


    /**
     * @var float
     *
     * @ORM\Column(name="priceOffline", type="float",nullable=true)
     * @Assert\GreaterThanOrEqual(0)
     */
    private $priceOffline;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Tax", inversedBy="productsOffline" , cascade={"persist"} )
     * @ORM\JoinTable(name="lilworks_products_taxesOffline")
     */
    private $taxesOffline;


    /**
     * @var float
     *
     * @ORM\Column(name="priceBuying", type="float",nullable=true)
     */
    private $priceBuying;

    /**
     * @var float
     *
     * @ORM\Column(name="priceRetail", type="float",nullable=true)
     */
    private $priceRetail;

    /**
     * @var string
     *
     * @ORM\Column(name="leadTime", type="string",length=255,nullable=true)
     */
    private $leadTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer",nullable=true)
     */
    private $stock;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alwaysAvailable", type="boolean",nullable=true)
     */
    private $alwaysAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSecondHand", type="boolean",nullable=true)
     */
    private $isSecondHand;

    /*
     * Product spec
     */
    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Docfile", inversedBy="products" ,cascade={"remove","persist"})
     * @ORM\JoinTable(name="lilworks_products_docfiles")
     */
    private $docfiles;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float",length=100,nullable=true)
     */
    private $weight;

    /**
     * @var float
     *
     * @ORM\Column(name="width", type="float",length=100,nullable=true)
     */
    private $width;

    /**
     * @var float
     *
     * @ORM\Column(name="length", type="float",length=100,nullable=true)
     */
    private $length;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float",length=100,nullable=true)
     */
    private $height;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isReviewable", type="boolean",nullable=true)
     */
    private $isReviewable;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ordersProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shippingMethods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->warrantiesOffline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->warrantiesOnline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productsRelated = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relatedProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->basketsProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pictures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->taxesOnline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->taxesOffline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->docfiles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Product
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
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Product
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
     * Set description
     *
     * @param string $description
     *
     * @return Product
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
     * @return Product
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
     * Set priceOnline
     *
     * @param float $priceOnline
     *
     * @return Product
     */
    public function setPriceOnline($priceOnline)
    {
        $this->priceOnline = $priceOnline;

        return $this;
    }

    /**
     * Get priceOnline
     *
     * @return float
     */
    public function getPriceOnline()
    {
        return $this->priceOnline;
    }

    /**
     * Set priceOffline
     *
     * @param float $priceOffline
     *
     * @return Product
     */
    public function setPriceOffline($priceOffline)
    {
        $this->priceOffline = $priceOffline;

        return $this;
    }

    /**
     * Get priceOffline
     *
     * @return float
     */
    public function getPriceOffline()
    {
        return $this->priceOffline;
    }

    /**
     * Set priceBuying
     *
     * @param float $priceBuying
     *
     * @return Product
     */
    public function setPriceBuying($priceBuying)
    {
        $this->priceBuying = $priceBuying;

        return $this;
    }

    /**
     * Get priceBuying
     *
     * @return float
     */
    public function getPriceBuying()
    {
        return $this->priceBuying;
    }

    /**
     * Set priceRetail
     *
     * @param float $priceRetail
     *
     * @return Product
     */
    public function setPriceRetail($priceRetail)
    {
        $this->priceRetail = $priceRetail;

        return $this;
    }

    /**
     * Get priceRetail
     *
     * @return float
     */
    public function getPriceRetail()
    {
        return $this->priceRetail;
    }

    /**
     * Set leadTime
     *
     * @param string $leadTime
     *
     * @return Product
     */
    public function setLeadTime($leadTime)
    {
        $this->leadTime = $leadTime;

        return $this;
    }

    /**
     * Get leadTime
     *
     * @return string
     */
    public function getLeadTime()
    {
        return $this->leadTime;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     *
     * @return Product
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set alwaysAvailable
     *
     * @param boolean $alwaysAvailable
     *
     * @return Product
     */
    public function setAlwaysAvailable($alwaysAvailable)
    {
        $this->alwaysAvailable = $alwaysAvailable;

        return $this;
    }

    /**
     * Get alwaysAvailable
     *
     * @return boolean
     */
    public function getAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    /**
     * Set isSecondHand
     *
     * @param boolean $isSecondHand
     *
     * @return Product
     */
    public function setIsSecondHand($isSecondHand)
    {
        $this->isSecondHand = $isSecondHand;

        return $this;
    }

    /**
     * Get isSecondHand
     *
     * @return boolean
     */
    public function getIsSecondHand()
    {
        return $this->isSecondHand;
    }

    /**
     * Set weight
     *
     * @param float $weight
     *
     * @return Product
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set width
     *
     * @param float $width
     *
     * @return Product
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set length
     *
     * @param float $length
     *
     * @return Product
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return float
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set height
     *
     * @param float $height
     *
     * @return Product
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set isReviewable
     *
     * @param boolean $isReviewable
     *
     * @return Product
     */
    public function setIsReviewable($isReviewable)
    {
        $this->isReviewable = $isReviewable;

        return $this;
    }

    /**
     * Get isReviewable
     *
     * @return boolean
     */
    public function getIsReviewable()
    {
        return $this->isReviewable;
    }

    /**
     * Set depositSale
     *
     * @param \LilWorks\StoreBundle\Entity\DepositSale $depositSale
     *
     * @return Product
     */
    public function setDepositSale(\LilWorks\StoreBundle\Entity\DepositSale $depositSale = null)
    {
        $this->depositSale = $depositSale;

        return $this;
    }

    /**
     * Get depositSale
     *
     * @return \LilWorks\StoreBundle\Entity\DepositSale
     */
    public function getDepositSale()
    {
        return $this->depositSale;
    }

    /**
     * Add category
     *
     * @param \LilWorks\StoreBundle\Entity\Category $category
     *
     * @return Product
     */
    public function addCategory(\LilWorks\StoreBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \LilWorks\StoreBundle\Entity\Category $category
     */
    public function removeCategory(\LilWorks\StoreBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add tag
     *
     * @param \LilWorks\StoreBundle\Entity\Tag $tag
     *
     * @return Product
     */
    public function addTag(\LilWorks\StoreBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \LilWorks\StoreBundle\Entity\Tag $tag
     */
    public function removeTag(\LilWorks\StoreBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set brand
     *
     * @param \LilWorks\StoreBundle\Entity\Brand $brand
     *
     * @return Product
     */
    public function setBrand(\LilWorks\StoreBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \LilWorks\StoreBundle\Entity\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Add ordersProduct
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersProducts $ordersProduct
     *
     * @return Product
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
     * Add shippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod
     *
     * @return Product
     */
    public function addShippingMethod(\LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod)
    {
        $this->shippingMethods[] = $shippingMethod;

        return $this;
    }

    /**
     * Remove shippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod
     */
    public function removeShippingMethod(\LilWorks\StoreBundle\Entity\ShippingMethod $shippingMethod)
    {
        $this->shippingMethods->removeElement($shippingMethod);
    }

    /**
     * Get shippingMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShippingMethods()
    {
        return $this->shippingMethods;
    }

    /**
     * Add warrantiesOffline
     *
     * @param \LilWorks\StoreBundle\Entity\Warranty $warrantiesOffline
     *
     * @return Product
     */
    public function addWarrantiesOffline(\LilWorks\StoreBundle\Entity\Warranty $warrantiesOffline)
    {
        $this->warrantiesOffline[] = $warrantiesOffline;

        return $this;
    }

    /**
     * Remove warrantiesOffline
     *
     * @param \LilWorks\StoreBundle\Entity\Warranty $warrantiesOffline
     */
    public function removeWarrantiesOffline(\LilWorks\StoreBundle\Entity\Warranty $warrantiesOffline)
    {
        $this->warrantiesOffline->removeElement($warrantiesOffline);
    }

    /**
     * Get warrantiesOffline
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWarrantiesOffline()
    {
        return $this->warrantiesOffline;
    }

    /**
     * Add warrantiesOnline
     *
     * @param \LilWorks\StoreBundle\Entity\Warranty $warrantiesOnline
     *
     * @return Product
     */
    public function addWarrantiesOnline(\LilWorks\StoreBundle\Entity\Warranty $warrantiesOnline)
    {
        $this->warrantiesOnline[] = $warrantiesOnline;

        return $this;
    }

    /**
     * Remove warrantiesOnline
     *
     * @param \LilWorks\StoreBundle\Entity\Warranty $warrantiesOnline
     */
    public function removeWarrantiesOnline(\LilWorks\StoreBundle\Entity\Warranty $warrantiesOnline)
    {
        $this->warrantiesOnline->removeElement($warrantiesOnline);
    }

    /**
     * Get warrantiesOnline
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWarrantiesOnline()
    {
        return $this->warrantiesOnline;
    }

    /**
     * Add productsRelated
     *
     * @param \LilWorks\StoreBundle\Entity\Product $productsRelated
     *
     * @return Product
     */
    public function addProductsRelated(\LilWorks\StoreBundle\Entity\Product $productsRelated)
    {
        $this->productsRelated[] = $productsRelated;

        return $this;
    }

    /**
     * Remove productsRelated
     *
     * @param \LilWorks\StoreBundle\Entity\Product $productsRelated
     */
    public function removeProductsRelated(\LilWorks\StoreBundle\Entity\Product $productsRelated)
    {
        $this->productsRelated->removeElement($productsRelated);
    }

    /**
     * Get productsRelated
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductsRelated()
    {
        return $this->productsRelated;
    }

    /**
     * Add relatedProduct
     *
     * @param \LilWorks\StoreBundle\Entity\Product $relatedProduct
     *
     * @return Product
     */
    public function addRelatedProduct(\LilWorks\StoreBundle\Entity\Product $relatedProduct)
    {
        $this->relatedProducts[] = $relatedProduct;

        return $this;
    }

    /**
     * Remove relatedProduct
     *
     * @param \LilWorks\StoreBundle\Entity\Product $relatedProduct
     */
    public function removeRelatedProduct(\LilWorks\StoreBundle\Entity\Product $relatedProduct)
    {
        $this->relatedProducts->removeElement($relatedProduct);
    }

    /**
     * Get relatedProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }

    /**
     * Add basketsProduct
     *
     * @param \LilWorks\StoreBundle\Entity\BasketsProducts $basketsProduct
     *
     * @return Product
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
     * Add picture
     *
     * @param \LilWorks\StoreBundle\Entity\Picture $picture
     *
     * @return Product
     */
    public function addPicture(\LilWorks\StoreBundle\Entity\Picture $picture)
    {
        $this->pictures[] = $picture;

        return $this;
    }

    /**
     * Remove picture
     *
     * @param \LilWorks\StoreBundle\Entity\Picture $picture
     */
    public function removePicture(\LilWorks\StoreBundle\Entity\Picture $picture)
    {
        $this->pictures->removeElement($picture);
    }

    /**
     * Get pictures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Add review
     *
     * @param \LilWorks\StoreBundle\Entity\Review $review
     *
     * @return Product
     */
    public function addReview(\LilWorks\StoreBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \LilWorks\StoreBundle\Entity\Review $review
     */
    public function removeReview(\LilWorks\StoreBundle\Entity\Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Add taxOnline
     *
     * @param \LilWorks\StoreBundle\Entity\Tax $taxOnline
     *
     * @return Product
     */
    public function addTaxOnline(\LilWorks\StoreBundle\Entity\Tax $taxOnline)
    {
        $this->taxesOnline[] = $taxOnline;

        return $this;
    }

    /**
     * Remove taxOnline
     *
     * @param \LilWorks\StoreBundle\Entity\Tax $taxOnline
     */
    public function removeTaxOnline(\LilWorks\StoreBundle\Entity\Tax $taxOnline)
    {
        $this->taxesOnline->removeElement($taxOnline);
    }

    /**
     * Get taxesOnline
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxesOnline()
    {
        return $this->taxesOnline;
    }

    /**
     * Add taxOffline
     *
     * @param \LilWorks\StoreBundle\Entity\Tax $taxOffline
     *
     * @return Product
     */
    public function addTaxOffline(\LilWorks\StoreBundle\Entity\Tax $taxOffline)
    {
        $this->taxesOffline[] = $taxOffline;

        return $this;
    }

    /**
     * Remove taxOffline
     *
     * @param \LilWorks\StoreBundle\Entity\Tax $taxOffline
     */
    public function removeTaxOffline(\LilWorks\StoreBundle\Entity\Tax $taxOffline)
    {
        $this->taxesOffline->removeElement($taxOffline);
    }

    /**
     * Get taxesOffline
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxesOffline()
    {
        return $this->taxesOffline;
    }

    /**
     * Add docfile
     *
     * @param \LilWorks\StoreBundle\Entity\Docfile $docfile
     *
     * @return Product
     */
    public function addDocfile(\LilWorks\StoreBundle\Entity\Docfile $docfile)
    {
        $this->docfiles[] = $docfile;

        return $this;
    }

    /**
     * Remove docfile
     *
     * @param \LilWorks\StoreBundle\Entity\Docfile $docfile
     */
    public function removeDocfile(\LilWorks\StoreBundle\Entity\Docfile $docfile)
    {
        $this->docfiles->removeElement($docfile);
    }

    /**
     * Get docfiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocfiles()
    {
        return $this->docfiles;
    }
}
