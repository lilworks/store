<?php
// src/AppBundle/Entity/User.php

namespace LilWorks\StoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_remoteUser")
 */
class RemoteUser extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * One RemoteUser is One Customer.
     * @ORM\OneToOne(targetEntity="LilWorks\StoreBundle\Entity\Customer", mappedBy="remoteUser", cascade={"remove","persist"})
     * @Assert\NotBlank(groups={"registration"})
     */
    private $customer;

    /**
     * @var integer
     *
     * @ORM\Column(name="remoteUserId", type="string",length=100,nullable=true)
     */
    private $remoteUserId;


    public function setEmail($email){
        parent::setEmail($email);
        $this->setUsername($email);
    }



    /**
     * Set customer
     *
     * @param \LilWorks\StoreBundle\Entity\Customer $customer
     *
     * @return User
     */
    public function setCustomer(\LilWorks\StoreBundle\Entity\Customer $customer = null)
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
     * Set remoteUserId
     *
     * @param string $remoteUserId
     *
     * @return RemoteUser
     */
    public function setRemoteUserId($remoteUserId)
    {
        $this->remoteUserId = $remoteUserId;

        return $this;
    }

    /**
     * Get remoteUserId
     *
     * @return string
     */
    public function getRemoteUserId()
    {
        return $this->remoteUserId;
    }
}
