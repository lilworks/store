<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_orders_ordersteps")
 * @ORM\HasLifecycleCallbacks()
 */
class OrdersOrderSteps
{
    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {

        if( is_null($this->createdAt)  ){
            $this->createdAt= new \DateTime();
        }
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Order", inversedBy="ordersOrderSteps")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\OrderStep", inversedBy="ordersOrderSteps")
     * @ORM\JoinColumn(name="orderStep", referencedColumnName="id", nullable=FALSE)
     */
    protected $orderStep;


    /**
     * @var datetime
     *
     * @ORM\Column(name="createdAt", type="datetime",nullable=true)
     */
    private $createdAt;



    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;





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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return OrdersOrderSteps
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
     * Set description
     *
     * @param string $description
     *
     * @return OrdersOrderSteps
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
     * Set order
     *
     * @param \LilWorks\StoreBundle\Entity\Order $order
     *
     * @return OrdersOrderSteps
     */
    public function setOrder(\LilWorks\StoreBundle\Entity\Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \LilWorks\StoreBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set orderStep
     *
     * @param \LilWorks\StoreBundle\Entity\OrderStep $orderStep
     *
     * @return OrdersOrderSteps
     */
    public function setOrderStep(\LilWorks\StoreBundle\Entity\OrderStep $orderStep)
    {
        $this->orderStep = $orderStep;

        return $this;
    }

    /**
     * Get orderStep
     *
     * @return \LilWorks\StoreBundle\Entity\OrderStep
     */
    public function getOrderStep()
    {
        return $this->orderStep;
    }
}
