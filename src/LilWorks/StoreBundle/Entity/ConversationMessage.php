<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_conversation_message")
 */
class ConversationMessage
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var datetime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $readedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="messageSubject", type="string",length=255,nullable=true)
     */
    private $messageSubject;
    /**
     * @var text
     *
     * @ORM\Column(name="messageSubject", type="text",nullable=false)
     */
    private $messageBody;
    /**
     * @var boolean
     *
     * @ORM\Column(name="getCopy", type="boolean",nullable=true)
     */
    private $getCopy;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isResponse", type="boolean",nullable=true)
     */
    private $isResponse;

    /**
    * @ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Conversation", inversedBy="messages")
    * @JoinColumn(name="conversation", referencedColumnName="id")
    */
    private $conversation;



}
