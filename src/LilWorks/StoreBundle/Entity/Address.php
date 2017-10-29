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
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string",length=255,nullable=false)
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
     */
    protected $country;



}
