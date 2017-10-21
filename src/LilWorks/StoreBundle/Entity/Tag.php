<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LilWorks\StoreBundle\Util\TagSanitizer;


/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_tag")
 * @ORM\HasLifecycleCallbacks()
 */
class Tag
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
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="LilWorks\StoreBundle\Entity\Product", mappedBy="tags")
     */
    private $products;



    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255,nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=100,nullable=false)
     */
    private $tag;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Tag
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
     * @return Tag
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
     * Add product
     *
     * @param \LilWorks\StoreBundle\Entity\Product $product
     *
     * @return Tag
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
}
