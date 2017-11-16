<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use LilWorks\StoreBundle\Util\TagSanitizer;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_shipping_method")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class ShippingMethod
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
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethodsCountries", mappedBy="shippingMethod" ,cascade={"persist"})
     */
    private $shippingmethods_countries;


    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\BasketsRealShippingMethods", mappedBy="shippingMethod" ,cascade={"remove","persist"})
     */
    private $basketsRealShippingMethods;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersRealShippingMethods", mappedBy="shippingMethod",cascade={"persist"})
     */
    private $ordersRealShippingMethods;

    /**
     * Many ShippingMethods have Many Products.
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="shippingMethods" ,cascade={"persist"})
     */
    private $products;

    /**
     * @Vich\UploadableField(mapping="shippingmethod_pictures", fileNameProperty="pictureName")
     *
     * @var File
     */
    private $pictureFile;

    /**
     * @var string
     *
     * @ORM\Column(name="pictureName", type="string",length=255,nullable=true)
     */
    private $pictureName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255,nullable=false)
     */
    private $name;

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
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=100,nullable=false)
     */
    private $tag;

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
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethodsTriggers", mappedBy="shippingMethod",cascade={"persist","remove"})
     * @ORM\OrderBy({ "trigger" = "ASC"})
     */
    private $triggers;


    /**
     * @var integer
     *
     * @ORM\Column(name="delay", type="integer",nullable=true)
     */
    private $delay;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean",nullable=true)
     */
    private $isPublished;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer",nullable=true)
     */
    private $priority;

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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return shippingMethod
     */
    public function setPictureFile(File $pictureFile = null)
    {
        $this->pictureFile = $pictureFile;

        if ($pictureFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getPictureFile()
    {
        return $this->pictureFile;
    }



    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shippingmethods_countries = new \Doctrine\Common\Collections\ArrayCollection();
        $this->realShippingMethods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getPriceByCountry($countryId){
        foreach($this->getShippingmethodsCountries() as $shippingmethodCountry){
            if($shippingmethodCountry->getCountry()->getId() == $countryId && $shippingmethodCountry->getPrice() ){
                return $shippingmethodCountry->getPrice();
            }
        }
        return $this->getPrice();
    }
    public function getFreeTriggerByCountry($countryId){
        foreach($this->getShippingmethodsCountries() as $shippingmethodCountry){
            if($shippingmethodCountry->getCountry()->getId() == $countryId && $shippingmethodCountry->getFreeTrigger() ){
                return $shippingmethodCountry->getFreeTrigger();
            }
        }
        return $this->getFreeTrigger();
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
     * Set pictureName
     *
     * @param string $pictureName
     *
     * @return ShippingMethod
     */
    public function setPictureName($pictureName)
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    /**
     * Get pictureName
     *
     * @return string
     */
    public function getPictureName()
    {
        return $this->pictureName;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ShippingMethod
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
     * Set description
     *
     * @param string $description
     *
     * @return ShippingMethod
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
     * @return ShippingMethod
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
     * Set tag
     *
     * @param string $tag
     *
     * @return ShippingMethod
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
     * Set price
     *
     * @param float $price
     *
     * @return ShippingMethod
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
     * @return ShippingMethod
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
     * Set delay
     *
     * @param integer $delay
     *
     * @return ShippingMethod
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
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return ShippingMethod
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
     * Set priority
     *
     * @param integer $priority
     *
     * @return ShippingMethod
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ShippingMethod
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
     * @return ShippingMethod
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
     * Add shippingmethodsCountry
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingmethodsCountry
     *
     * @return ShippingMethod
     */
    public function addShippingmethodsCountry(\LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingmethodsCountry)
    {
        $this->shippingmethods_countries[] = $shippingmethodsCountry;

        return $this;
    }

    /**
     * Remove shippingmethodsCountry
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingmethodsCountry
     */
    public function removeShippingmethodsCountry(\LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingmethodsCountry)
    {
        $this->shippingmethods_countries->removeElement($shippingmethodsCountry);
    }

    /**
     * Get shippingmethodsCountries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShippingmethodsCountries()
    {
        return $this->shippingmethods_countries;
    }

    /**
     * Add basketsRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\BasketsRealShippingMethods $basketsRealShippingMethod
     *
     * @return ShippingMethod
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
     * Add ordersRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $ordersRealShippingMethod
     *
     * @return ShippingMethod
     */
    public function addOrdersRealShippingMethod(\LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $ordersRealShippingMethod)
    {
        $this->ordersRealShippingMethods[] = $ordersRealShippingMethod;

        return $this;
    }

    /**
     * Remove ordersRealShippingMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $ordersRealShippingMethod
     */
    public function removeOrdersRealShippingMethod(\LilWorks\StoreBundle\Entity\OrdersRealShippingMethods $ordersRealShippingMethod)
    {
        $this->ordersRealShippingMethods->removeElement($ordersRealShippingMethod);
    }

    /**
     * Get ordersRealShippingMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrdersRealShippingMethods()
    {
        return $this->ordersRealShippingMethods;
    }

    /**
     * Add product
     *
     * @param \LilWorks\StoreBundle\Entity\Product $product
     *
     * @return ShippingMethod
     */
    public function addProduct(\LilWorks\StoreBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \LilWorks\StoreBundle\Entity\Product $product
     */
    public function removeProduct(\LilWorks\StoreBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add trigger
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethodsTriggers $trigger
     *
     * @return ShippingMethod
     */
    public function addTrigger(\LilWorks\StoreBundle\Entity\ShippingMethodsTriggers $trigger)
    {
        $this->triggers[] = $trigger;

        return $this;
    }

    /**
     * Remove trigger
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethodsTriggers $trigger
     */
    public function removeTrigger(\LilWorks\StoreBundle\Entity\ShippingMethodsTriggers $trigger)
    {
        $this->triggers->removeElement($trigger);
    }

    /**
     * Get triggers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTriggers()
    {
        return $this->triggers;
    }
}
