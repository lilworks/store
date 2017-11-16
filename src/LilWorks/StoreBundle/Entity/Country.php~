<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_country")
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\ShippingMethodsCountries", mappedBy="country" ,cascade={"persist"})
     */
    private $shippingmethods_countries;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Address", mappedBy="country" ,cascade={"remove","persist"})
     */
    private $addresses;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255,nullable=false)
     * @Assert\NotBlank()
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="flag", type="string",length=255,nullable=true)
     */
    private $flag;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=2,nullable=false)
     * @Assert\NotBlank()
     */
    private $tag;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean",nullable=true)
     */
    private $isPublished;



    public function addShippingmethodsCountrie(ShippingMethodsCountries $shippingmethodsCountrie)
    {
        $this->shippingmethodsCountrie->add($shippingmethodsCountrie);
    }

    public function removeShippingmethodsCountrie(ShippingMethodsCountries $shippingmethodsCountrie)
    {
        $this->shippingmethodsCountrie->removeElement($shippingmethodsCountrie);
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shippingmethods_countries = new \Doctrine\Common\Collections\ArrayCollection();
        $this->addresses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Country
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
     * Set flag
     *
     * @param string $flag
     *
     * @return Country
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Get flag
     *
     * @return string
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set tag
     *
     * @param string $tag
     *
     * @return Country
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
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Country
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
     * Add shippingmethodsCountry
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingmethodsCountry
     *
     * @return Country
     */
    public function addShippingmethodsCountry(\LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingmethodsCountry)
    {
        $this->shippingmethods_countries[] = $shippingmethodsCountry;

        return $this;
    }

    /**
     * Remove shippingmethodsCountry
     *
     * @param \LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingmethodsCountry
     */
    public function removeShippingmethodsCountry(\LilWorks\StoreBundle\Entity\ShippingMethodsCountries $shippingmethodsCountry)
    {
        $this->shippingmethods_countries->removeElement($shippingmethodsCountry);
    }

    /**
     * Get shippingmethodsCountries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShippingmethodsCountries()
    {
        return $this->shippingmethods_countries;
    }

    /**
     * Add address
     *
     * @param \LilWorks\StoreBundle\Entity\Address $address
     *
     * @return Country
     */
    public function addAddress(\LilWorks\StoreBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \LilWorks\StoreBundle\Entity\Address $address
     */
    public function removeAddress(\LilWorks\StoreBundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }
}
