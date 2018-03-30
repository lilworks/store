<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_address")
 */
class Address
{
    public function __clone(){
        if ($this->id) {
            $this->id = null;
        }
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Customer", inversedBy="addresses")
     * @ORM\JoinColumn(name="customer", referencedColumnName="id" , nullable=false)
     */
    protected $customer;



    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255,nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string",length=255,nullable=true)
     */
    private $companyName;




    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string",length=255,nullable=true)
     * @Assert\NotBlank()
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="complement", type="string",length=255,nullable=true)
     */
    private $complement;

    /**
     * @var string
     *
     * @ORM\Column(name="zipCode", type="string",nullable=false)
     * @Assert\NotBlank()
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string",length=255,nullable=false)
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Country", inversedBy="addresses")
     * @ORM\JoinColumn(name="country", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    protected $country;




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
     * @return Address
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
     * Set companyName
     *
     * @param string $companyName
     *
     * @return Address
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set complement
     *
     * @param string $complement
     *
     * @return Address
     */
    public function setComplement($complement)
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * Get complement
     *
     * @return string
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Address
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Address
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
     * Set customer
     *
     * @param \LilWorks\StoreBundle\Entity\Customer $customer
     *
     * @return Address
     */
    public function setCustomer(\LilWorks\StoreBundle\Entity\Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \LilWorks\StoreBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set country
     *
     * @param \LilWorks\StoreBundle\Entity\Country $country
     *
     * @return Address
     */
    public function setCountry(\LilWorks\StoreBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \LilWorks\StoreBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }
}
