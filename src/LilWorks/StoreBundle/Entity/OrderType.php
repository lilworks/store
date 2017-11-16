<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_ordertype")
 */
class OrderType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Order", mappedBy="orderType" )
     */
    private $orders;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=100,nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=100,nullable=false)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string",length=3,nullable=false)
     */
    private $prefix;

    /**
     * @var boolean
     *
     * @ORM\Column(name="destocking", type="boolean",length=3,nullable=true)
     */
    private $destocking;




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
     * Set name
     *
     * @param string $name
     *
     * @return OrderType
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
     * @return OrderType
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
     * @return OrderType
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

    /**
     * Set destocking
     *
     * @param boolean $destocking
     *
     * @return OrderType
     */
    public function setDestocking($destocking)
    {
        $this->destocking = $destocking;

        return $this;
    }

    /**
     * Get destocking
     *
     * @return boolean
     */
    public function getDestocking()
    {
        return $this->destocking;
    }

    /**
     * Add order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     *
     * @return OrderType
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
}
