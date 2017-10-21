<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use LilWorks\StoreBundle\Util\TagSanitizer;


/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_category")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Category
{

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $sanitizer = new TagSanitizer();
        $this->tag = $sanitizer->sanitize($this->getName());
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;



    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="categories",cascade={"remove","persist"} )
     * @ORM\OrderBy({ "brand" = "ASC","name" = "ASC"})
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\SuperCategoriesCategories", mappedBy="category" ,cascade={"remove","persist"})
     */
    private $supercategories_categories;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean",nullable=true)
     */
    private $isPublished;

    /**
     * @Vich\UploadableField(mapping="brand_pictures", fileNameProperty="pictureName")
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
     * @ORM\Column(name="name", type="string",length=255,nullable=true)
     */
    private $name;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=100,nullable=false)
     */
    private $tag;


    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return brand
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
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->warrantiesOffline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->warrantiesOnline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->supercategories_categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Category
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
     * Set pictureName
     *
     * @param string $pictureName
     *
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Category
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
     * Add product
     *
     * @param \LilWorks\StoreBundle\Entity\Product $product
     *
     * @return Category
     */
    public function addProduct(\LilWorks\StoreBundle\Entity\Product $product)
    {
        $product->addCategory($this);
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
        $product->removeCategory($this);
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
     * Add supercategoriesCategory
     *
     * @param \LilWorks\StoreBundle\Entity\SuperCategoriesCategories $supercategoriesCategory
     *
     * @return Category
     */
    public function addSupercategoriesCategory(\LilWorks\StoreBundle\Entity\SuperCategoriesCategories $supercategoriesCategory)
    {
        $this->supercategories_categories[] = $supercategoriesCategory;

        return $this;
    }

    /**
     * Remove supercategoriesCategory
     *
     * @param \LilWorks\StoreBundle\Entity\SuperCategoriesCategories $supercategoriesCategory
     */
    public function removeSupercategoriesCategory(\LilWorks\StoreBundle\Entity\SuperCategoriesCategories $supercategoriesCategory)
    {
        $this->supercategories_categories->removeElement($supercategoriesCategory);
    }

    /**
     * Get supercategoriesCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSupercategoriesCategories()
    {
        return $this->supercategories_categories;
    }
}
