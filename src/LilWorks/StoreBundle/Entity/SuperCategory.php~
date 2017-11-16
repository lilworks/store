<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use LilWorks\StoreBundle\Util\TagSanitizer;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_supercategory")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="LilWorks\StoreBundle\Entity\Repository\SuperCategoryRepository")
 */
class SuperCategory
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
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\SuperCategoriesCategories", mappedBy="supercategory" ,cascade={"remove" ,"persist"})
     * @ORM\OrderBy({ "pos" = "ASC"})
     */
    private $supercategories_categories;

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
     * @var integer
     *
     * @ORM\Column(name="pos", type="integer",length=255,nullable=true)
     */
    private $pos;

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
     * @Vich\UploadableField(mapping="supercategory_pictures", fileNameProperty="pictureName")
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
     * Set tag
     *
     * @param string $tag
     *
     * @return SuperCategory
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
     * @return SuperCategory
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
     * Set pos
     *
     * @param integer $pos
     *
     * @return SuperCategory
     */
    public function setPos($pos)
    {
        $this->pos = $pos;

        return $this;
    }

    /**
     * Get pos
     *
     * @return integer
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return SuperCategory
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
     * @return SuperCategory
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
     * Set picture
     *
     * @param string $picture
     *
     * @return SuperCategory
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Add supercategoriesCategory
     *
     * @param \LilWorks\StoreBundle\Entity\SuperCategoriesCategories $supercategoriesCategory
     *
     * @return SuperCategory
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

    /**
     * Set pictureName
     *
     * @param string $pictureName
     *
     * @return SuperCategory
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
}
