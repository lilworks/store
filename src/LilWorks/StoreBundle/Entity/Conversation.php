<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_conversation")
 */
class Conversation
{

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
     * @var boolean
     *
     * @ORM\Column(name="sendmail", type="boolean",nullable=true)
     */
    private $sendmail;

    /**
     * @ManyToOne(targetEntity="AppBundle\Entity\User")
     * @JoinColumn(name="user", referencedColumnName="id" , nullable=true)
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isArchived", type="boolean",nullable=true)
     */
    private $isArchived;


    /**
     * @OneToMany(targetEntity="LilWorks\StoreBundle\Entity\ConversationMessage", mappedBy="conversation")
     */
    private $messages;

}
