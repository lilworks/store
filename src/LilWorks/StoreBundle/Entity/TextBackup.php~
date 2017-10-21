<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_text_backup")
 * @ORM\HasLifecycleCallbacks()
 */
class TextBackup
{
    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if($this->createdAt == null)
            $this->createdAt= new \DateTime();
        $this->updatedAt= new \DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LilWorks\StoreBundle\Entity\Text", inversedBy="backups" ,cascade={"remove"} )
     * @ORM\JoinColumn(name="originalText", referencedColumnName="id",nullable=true)
     */
    private $originalText;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string",length=255,nullable=true)
     */
    private $title;
    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text" ,nullable=true)
     */
    private $content;
    /**
     * @var text
     *
     * @ORM\Column(name="css", type="text",nullable=true)
     */
    private $css;
    /**
     * @var datetime
     *
     * @ORM\Column(name="updatedAt", type="datetime",nullable=true)
     */
    private $updatedAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="createdAt", type="datetime",nullable=true)
     */
    private $createdAt;




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
     * Set title
     *
     * @param string $title
     *
     * @return TextBackup
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return TextBackup
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set css
     *
     * @param string $css
     *
     * @return TextBackup
     */
    public function setCss($css)
    {
        $this->css = $css;

        return $this;
    }

    /**
     * Get css
     *
     * @return string
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return TextBackup
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return TextBackup
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
     * Set originalText
     *
     * @param \LilWorks\StoreBundle\Entity\Text $originalText
     *
     * @return TextBackup
     */
    public function setOriginalText(\LilWorks\StoreBundle\Entity\Text $originalText = null)
    {
        $this->originalText = $originalText;

        return $this;
    }

    /**
     * Get originalText
     *
     * @return \LilWorks\StoreBundle\Entity\Text
     */
    public function getOriginalText()
    {
        return $this->originalText;
    }
}
