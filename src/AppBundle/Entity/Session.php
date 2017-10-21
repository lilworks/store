<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Yaml\Yaml;
/**
 * @ORM\Entity
 * @ORM\Table(name="sessions")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SessionRepository")
 */
class Session
{

    public function getReadableData(){
        return Yaml::parse(stream_get_contents($this->getData()));
        #str_replace('_sf2_attributes|','',stream_get_contents($this->getData()));

    }
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=128,name="sess_id")
     */
    protected $id;



    /**
     * @var blob
     *
     * @ORM\Column(name="sess_data", type="blob" , nullable=false)
     */
    private $data;

    /**
     * @var integer
     *
     * @ORM\Column(name="sess_time", type="integer" , nullable=true)
     */
    private $time;


    /**
     * @var integer
     *
     * @ORM\Column(name="sess_lifetime", type="integer" , nullable=true)
     */
    private $lifetime;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sessions")
     * @ORM\JoinColumn(name="user", referencedColumnName="id" , nullable = true)
     */
    private $user;


    /**
     * One User is One Session Token.
     * @ORM\OneToOne(targetEntity="LilWorks\StoreBundle\Entity\Basket", mappedBy="token", cascade={"remove","persist"})
     */
    private $basket;

    

    

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Session
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Session
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set time
     *
     * @param integer $time
     *
     * @return Session
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set lifetime
     *
     * @param integer $lifetime
     *
     * @return Session
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * Get lifetime
     *
     * @return integer
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Session
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

    /**
     * Set basket
     *
     * @param \LilWorks\StoreBundle\Entity\Basket $basket
     *
     * @return Session
     */
    public function setBasket(\LilWorks\StoreBundle\Entity\Basket $basket = null)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get basket
     *
     * @return \LilWorks\StoreBundle\Entity\Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }
}
