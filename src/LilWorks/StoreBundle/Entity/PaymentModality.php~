<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_payment_modality")
 */
class PaymentModality
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Order", mappedBy="payment_modality")
     */
    private $orders;

    /**
     * @var string
     *
     * @ORM\Column(name="modality", type="string",length=100,nullable=false)
     */
    private $modality;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublishedOnline", type="boolean",nullable=true)
     */
    private $isPublishedOnline;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublishedOffline", type="boolean",nullable=true)
     */
    private $isPublishedOffline;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;




    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set modality
     *
     * @param string $modality
     *
     * @return PaymentModality
     */
    public function setModality($modality)
    {
        $this->modality = $modality;

        return $this;
    }

    /**
     * Get modality
     *
     * @return string
     */
    public function getModality()
    {
        return $this->modality;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return PaymentModality
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
     * Add order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     *
     * @return PaymentModality
     */
    public function addOrder(\LilWorks\StoreBundle\Entity\Order $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     */
    public function removeOrder(\LilWorks\StoreBundle\Entity\Order $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set isPublishedOnline
     *
     * @param boolean $isPublishedOnline
     *
     * @return PaymentModality
     */
    public function setIsPublishedOnline($isPublishedOnline)
    {
        $this->isPublishedOnline = $isPublishedOnline;

        return $this;
    }

    /**
     * Get isPublishedOnline
     *
     * @return boolean
     */
    public function getIsPublishedOnline()
    {
        return $this->isPublishedOnline;
    }

    /**
     * Set isPublishedOffline
     *
     * @param boolean $isPublishedOffline
     *
     * @return PaymentModality
     */
    public function setIsPublishedOffline($isPublishedOffline)
    {
        $this->isPublishedOffline = $isPublishedOffline;

        return $this;
    }

    /**
     * Get isPublishedOffline
     *
     * @return boolean
     */
    public function getIsPublishedOffline()
    {
        return $this->isPublishedOffline;
    }
}
