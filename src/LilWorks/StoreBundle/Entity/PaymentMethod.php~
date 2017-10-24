<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_payment_method")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class PaymentMethod
{
    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if($this->updatedAt == null)
            $this->updatedAt = new \DateTime();

    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersPaymentMethods", mappedBy="paymentMethod" ,cascade={"persist"})
     */
    private $ordersPaymentMethods;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=100,nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=100,nullable=true)
     */
    private $tag;
    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string",length=5,nullable=true)
     */
    private $prefix;
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
     * @var array
     *
     * @ORM\Column(name="datas", type="array" , nullable=true)
     */
    private $datas;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="paymentmethod_pictures", fileNameProperty="pictureName")
     *
     * @var File
     */
    private $pictureFile;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     *
     * @var string
     */
    private $pictureName;

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
    public function setPictureFile(File $pictureFile = null)
    {
        $this->pictureFile = $pictureFile;

        if ($pictureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
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
        $this->ordersPaymentMethods = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return PaymentMethod
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
     * @return PaymentMethod
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
     * @return PaymentMethod
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
     * Set datas
     *
     * @param array $datas
     *
     * @return PaymentMethod
     */
    public function setDatas($datas)
    {
        $this->datas = $datas;

        return $this;
    }

    /**
     * Get datas
     *
     * @return array
     */
    public function getDatas()
    {
        return $this->datas;
    }

    /**
     * Set pictureName
     *
     * @param string $pictureName
     *
     * @return PaymentMethod
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return PaymentMethod
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
     * Add ordersPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod
     *
     * @return PaymentMethod
     */
    public function addOrdersPaymentMethod(\LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod)
    {
        $this->ordersPaymentMethods[] = $ordersPaymentMethod;

        return $this;
    }

    /**
     * Remove ordersPaymentMethod
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod
     */
    public function removeOrdersPaymentMethod(\LilWorks\StoreBundle\Entity\OrdersPaymentMethods $ordersPaymentMethod)
    {
        $this->ordersPaymentMethods->removeElement($ordersPaymentMethod);
    }

    /**
     * Get ordersPaymentMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrdersPaymentMethods()
    {
        return $this->ordersPaymentMethods;
    }

    /**
     * Set tag
     *
     * @param string $tag
     *
     * @return PaymentMethod
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
     * Set prefix
     *
     * @param string $prefix
     *
     * @return PaymentMethod
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
}
