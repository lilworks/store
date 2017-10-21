<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_supercategories_categories")
 */
class SuperCategoriesCategories
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\SuperCategory", inversedBy="supercategories_categories")
     * @ORM\JoinColumn(name="supercategory", referencedColumnName="id", nullable=FALSE)
     */
    protected $supercategory;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Category", inversedBy="supercategories_categories")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=FALSE)
     */
    protected $category;


    /**
     * @var integer
     *
     * @ORM\Column(name="pos", type="integer",length=255,nullable=true)
     */
    private $pos;






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
     * Set pos
     *
     * @param integer $pos
     *
     * @return SuperCategoriesCategories
     */
    public function setPos($pos)
    {
        $this->pos = $pos;

        return $this;
    }

    /**
     * Get pos
     *
     * @return integer
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * Set supercategory
     *
     * @param \LilWorks\StoreBundle\Entity\SuperCategory $supercategory
     *
     * @return SuperCategoriesCategories
     */
    public function setSupercategory(\LilWorks\StoreBundle\Entity\SuperCategory $supercategory)
    {
        $this->supercategory = $supercategory;

        return $this;
    }

    /**
     * Get supercategory
     *
     * @return \LilWorks\StoreBundle\Entity\SuperCategory
     */
    public function getSupercategory()
    {
        return $this->supercategory;
    }

    /**
     * Set category
     *
     * @param \LilWorks\StoreBundle\Entity\Category $category
     *
     * @return SuperCategoriesCategories
     */
    public function setCategory(\LilWorks\StoreBundle\Entity\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \LilWorks\StoreBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
