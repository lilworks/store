<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_phonenumber")
 */
class PhoneNumber
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
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Customer", inversedBy="phonenumbers",cascade={"persist"})
     * @ORM\JoinColumn(name="customer", referencedColumnName="id" , nullable=false)
     */
    protected $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="phonenumber", type="string",length=255,nullable=false)
     */
    private $phonenumber;


    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text" ,nullable=true)
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
     * Set phonenumber
     *
     * @param string $phonenumber
     *
     * @return PhoneNumber
     */
    public function setPhonenumber($phonenumber)
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }

    /**
     * Get phonenumber
     *
     * @return string
     */
    public function getPhonenumber()
    {
        return $this->phonenumber;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return PhoneNumber
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
     * @return PhoneNumber
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
}
