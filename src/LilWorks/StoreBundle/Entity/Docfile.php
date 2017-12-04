<?php
namespace LilWorks\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_docfile")
 * @Vich\Uploadable
 */
class Docfile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="docfiles")
     */
    private $products;

    /**
     * @var text
     *
     * @ORM\Column(name="title", type="string",length=255,nullable=true)
     * @Assert\NotBlank()
     */
    private $title;
    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",length=255,nullable=true)
     */
    private $description;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="product_docfiles", fileNameProperty="docName", size="docSize")
     *
     * @var File
     */
    private $docFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $docName;

    /**
     * @ORM\Column(type="integer",nullable=true)
     *
     * @var integer
     */
    private $docSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Product
     */
    public function setDocFile(File $doc = null)
    {
        $this->docFile = $doc;

        if ($doc) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getDocFile()
    {
        return $this->docFile;
    }

    /**
     * @param string $docName
     *
     * @return DocFile
     */
    public function setDocName($docName)
    {
        $this->docName = $docName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocName()
    {
        return $this->docName;
    }

    /**
     * @param integer $docSize
     *
     * @return DocFile
     */
    public function setDocSize($docSize)
    {
        $this->docSize = $docSize;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getDocSize()
    {
        return $this->docSize;
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
     * Set description
     *
     * @param string $description
     *
     * @return Docfile
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Docfile
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
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add product
     *
     * @param \LilWorks\StoreBundle\Entity\Product $product
     *
     * @return Docfile
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
     * Set title
     *
     * @param string $title
     *
     * @return Docfile
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
