<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{

    public function cloneUser(){
        if ($this->id) {
            $this->id = null;
            return $this;
        }
    }
    public function setEmail($email){
        parent::setEmail($email);
        $this->setUsername($email);
    }


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Basket", mappedBy="user" )
     */
    private $baskets;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Conversation", mappedBy="user")
     */
    private $conversations;

    /**
     * One User is One Customer.
     * @ORM\OneToOne(targetEntity="LilWorks\StoreBundle\Entity\Customer", mappedBy="user", cascade={"remove","persist"})
     * @Assert\NotBlank(groups={"registration"})
     */
    private $customer;
    /**
     * @ORM\OneToOne(targetEntity="LilWorks\StoreBundle\Entity\Subscriber", mappedBy="user", cascade={"remove","persist"})
     */
    private $subscriber;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Order", mappedBy="userAsSeller")
     */
    private $createdOrders;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\Review", mappedBy="user")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="Session", mappedBy="user")
     */
    private $sessions;


    /**
     * Add basket
     *
     * @param \LilWorks\StoreBundle\Entity\Basket $basket
     *
     * @return User
     */
    public function addBasket(\LilWorks\StoreBundle\Entity\Basket $basket)
    {
        $this->baskets[] = $basket;

        return $this;
    }

    /**
     * Remove basket
     *
     * @param \LilWorks\StoreBundle\Entity\Basket $basket
     */
    public function removeBasket(\LilWorks\StoreBundle\Entity\Basket $basket)
    {
        $this->baskets->removeElement($basket);
    }

    /**
     * Get baskets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBaskets()
    {
        return $this->baskets;
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
     * Add createdOrder
     *
     * @param \LilWorks\StoreBundle\Entity\Order $createdOrder
     *
     * @return User
     */
    public function addCreatedOrder(\LilWorks\StoreBundle\Entity\Order $createdOrder)
    {
        $this->createdOrders[] = $createdOrder;

        return $this;
    }

    /**
     * Remove createdOrder
     *
     * @param \LilWorks\StoreBundle\Entity\Order $createdOrder
     */
    public function removeCreatedOrder(\LilWorks\StoreBundle\Entity\Order $createdOrder)
    {
        $this->createdOrders->removeElement($createdOrder);
    }

    /**
     * Get createdOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreatedOrders()
    {
        return $this->createdOrders;
    }

    /**
     * Add review
     *
     * @param \LilWorks\StoreBundle\Entity\Review $review
     *
     * @return User
     */
    public function addReview(\LilWorks\StoreBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \LilWorks\StoreBundle\Entity\Review $review
     */
    public function removeReview(\LilWorks\StoreBundle\Entity\Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Add session
     *
     * @param \AppBundle\Entity\Session $session
     *
     * @return User
     */
    public function addSession(\AppBundle\Entity\Session $session)
    {
        $this->sessions[] = $session;

        return $this;
    }

    /**
     * Remove session
     *
     * @param \AppBundle\Entity\Session $session
     */
    public function removeSession(\AppBundle\Entity\Session $session)
    {
        $this->sessions->removeElement($session);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Add conversation
     *
     * @param \LilWorks\StoreBundle\Entity\Conversation $conversation
     *
     * @return User
     */
    public function addConversation(\LilWorks\StoreBundle\Entity\Conversation $conversation)
    {
        $this->conversations[] = $conversation;

        return $this;
    }

    /**
     * Remove conversation
     *
     * @param \LilWorks\StoreBundle\Entity\Conversation $conversation
     */
    public function removeConversation(\LilWorks\StoreBundle\Entity\Conversation $conversation)
    {
        $this->conversations->removeElement($conversation);
    }

    /**
     * Get conversations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConversations()
    {
        return $this->conversations;
    }

    /**
     * Set subscriber
     *
     * @param \LilWorks\StoreBundle\Entity\Subscriber $subscriber
     *
     * @return User
     */
    public function setSubscriber(\LilWorks\StoreBundle\Entity\Subscriber $subscriber = null)
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * Get subscriber
     *
     * @return \LilWorks\StoreBundle\Entity\Subscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }
}
