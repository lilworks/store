<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_online_destocking")
 * @ORM\HasLifecycleCallbacks()
 */
class OnlineDestocking
{

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if(!$this->destockedAt)
            $this->destockedAt = new \DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\OneToOne(targetEntity="LilWorks\StoreBundle\Entity\OrdersProducts")
     * @ORM\JoinColumn(name="orderProduct", referencedColumnName="id")
     */
    private $orderProduct;



    /**
     * @var datetime
     *
     * @ORM\Column(name="destockedAt", type="datetime",nullable=true)
     */
    private $destockedAt;




    

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
     * Set destockedAt
     *
     * @param \DateTime $destockedAt
     *
     * @return OnlineDestocking
     */
    public function setDestockedAt($destockedAt)
    {
        $this->destockedAt = $destockedAt;

        return $this;
    }

    /**
     * Get destockedAt
     *
     * @return \DateTime
     */
    public function getDestockedAt()
    {
        return $this->destockedAt;
    }

    /**
     * Set orderProduct
     *
     * @param \LilWorks\StoreBundle\Entity\OrdersProducts $orderProduct
     *
     * @return OnlineDestocking
     */
    public function setOrderProduct(\LilWorks\StoreBundle\Entity\OrdersProducts $orderProduct = null)
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }

    /**
     * Get orderProduct
     *
     * @return \LilWorks\StoreBundle\Entity\OrdersProducts
     */
    public function getOrderProduct()
    {
        return $this->orderProduct;
    }
}
