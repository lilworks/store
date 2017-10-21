<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_review")
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Product", inversedBy="reviews")
     * @ORM\JoinColumn(name="product", referencedColumnName="id" , nullable=true)
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="reviews")
     * @ORM\JoinColumn(name="user", referencedColumnName="id" , nullable=true)
     */
    protected $user;

    /**
     * @var text
     *
     * @ORM\Column(name="rate", type="integer",nullable=true)
     */
    private $rate;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="string",length=255,nullable=true)
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
     * Set rate
     *
     * @param integer $rate
     *
     * @return Review
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return integer
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Review
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
     * Set product
     *
     * @param \LilWorks\StoreBundle\Entity\Product $product
     *
     * @return Review
     */
    public function setProduct(\LilWorks\StoreBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \LilWorks\StoreBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Review
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
