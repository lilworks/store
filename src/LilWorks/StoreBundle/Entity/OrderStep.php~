<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_orderstep")
 */
class OrderStep
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\OrdersOrderSteps", mappedBy="orderStep" ,cascade={"persist"})
     */
    private $ordersOrderSteps;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=100,nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=100)
     */
    private $tag;

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
        $this->ordersOrderSteps = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return OrderStep
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
     * @return OrderStep
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
     * Add ordersOrderStep
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersOrderSteps $ordersOrderStep
     *
     * @return OrderStep
     */
    public function addOrdersOrderStep(\LilWorks\StoreBundle\Entity\OrdersOrderSteps $ordersOrderStep)
    {
        $this->ordersOrderSteps[] = $ordersOrderStep;

        return $this;
    }

    /**
     * Remove ordersOrderStep
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersOrderSteps $ordersOrderStep
     */
    public function removeOrdersOrderStep(\LilWorks\StoreBundle\Entity\OrdersOrderSteps $ordersOrderStep)
    {
        $this->ordersOrderSteps->removeElement($ordersOrderStep);
    }

    /**
     * Get ordersOrderSteps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrdersOrderSteps()
    {
        return $this->ordersOrderSteps;
    }

    /**
     * Set tag
     *
     * @param string $tag
     *
     * @return OrderStep
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
}
