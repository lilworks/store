<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_conversation")
 * @ORM\HasLifecycleCallbacks
 */
class Conversation
{

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $unreaded = 0;
        foreach($this->getMessages() as $message){
            if( $message->getIsResponse() !=1  && is_null($message->getReadedAt())){
                $unreaded+=1;
            }
        }

        $this->setUnreaded($unreaded);

        if($this->getCreatedAt() == null)
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string",length=255,nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="conversationSubject", type="string",length=255,nullable=false)
     */
    private $conversationSubject;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sendmail", type="boolean",nullable=true)
     */
    private $sendmail;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id" , nullable=true)
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isArchived", type="boolean",nullable=true)
     */
    private $isArchived;

    /**
     * @var integer
     *
     * @ORM\Column(name="unreaded", type="integer",nullable=true)
     */
    private $unreaded;


    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\ConversationMessage", mappedBy="conversation",cascade={"persist","remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $messages;

    /**
     * @var datetime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return Conversation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set sendmail
     *
     * @param boolean $sendmail
     *
     * @return Conversation
     */
    public function setSendmail($sendmail)
    {
        $this->sendmail = $sendmail;

        return $this;
    }

    /**
     * Get sendmail
     *
     * @return boolean
     */
    public function getSendmail()
    {
        return $this->sendmail;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     *
     * @return Conversation
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Conversation
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
     * Add message
     *
     * @param \LilWorks\StoreBundle\Entity\ConversationMessage $message
     *
     * @return Conversation
     */
    public function addMessage(\LilWorks\StoreBundle\Entity\ConversationMessage $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \LilWorks\StoreBundle\Entity\ConversationMessage $message
     */
    public function removeMessage(\LilWorks\StoreBundle\Entity\ConversationMessage $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set conversationSubject
     *
     * @param string $conversationSubject
     *
     * @return Conversation
     */
    public function setConversationSubject($conversationSubject)
    {
        $this->conversationSubject = $conversationSubject;

        return $this;
    }

    /**
     * Get conversationSubject
     *
     * @return string
     */
    public function getConversationSubject()
    {
        return $this->conversationSubject;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Conversation
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set unreaded
     *
     * @param integer $unreaded
     *
     * @return Conversation
     */
    public function setUnreaded($unreaded)
    {
        $this->unreaded = $unreaded;

        return $this;
    }

    /**
     * Get unreaded
     *
     * @return integer
     */
    public function getUnreaded()
    {
        return $this->unreaded;
    }
}
