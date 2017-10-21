<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use LilWorks\StoreBundle\Util\TagSanitizer;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_annonce")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Annonce
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
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $this->updatedAt= new \DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;



    /**
     * @Vich\UploadableField(mapping="annonce_pictures", fileNameProperty="pictureName")
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
     * @ORM\Column(name="tag", type="string",length=255,nullable=false)
     */
    private $tag;

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
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean",nullable=true)
     */
    private $isPublished;

    /**
     * @var string
     *
     * @ORM\Column(name="pos", type="integer",nullable=true)
     */
    private $pos;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string",length=100,nullable=true)
     */
    private $link;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updatedAt", type="datetime",nullable=true)
     */
    private $updatedAt;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }



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
     * @return Annonce
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
     * @return Annonce
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
     * @return Annonce
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
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Annonce
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
     * Set pos
     *
     * @param integer $pos
     *
     * @return Annonce
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Annonce
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Annonce
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
     * Set link
     *
     * @param string $link
     *
     * @return Annonce
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }
}
