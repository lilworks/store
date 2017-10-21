<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_depositSale_status")
 */
class DepositSaleStatus
{



    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;



    /**
     * @var text
     *
     * @ORM\Column(name="name", type="text",length=20, nullable=false)
     */
    private $name;


    /**
     * @var text
     *
     * @ORM\Column(name="tag", type="text",length=20, nullable=false)
     */
    private $tag;
    /**
     * @var text
     *
     * @ORM\Column(name="color", type="text",length=20, nullable=true)
     */
    private $color;


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
     * @return DepositSaleStatus
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
     * @return DepositSaleStatus
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
     * Set color
     *
     * @param string $color
     *
     * @return DepositSaleStatus
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }
}
