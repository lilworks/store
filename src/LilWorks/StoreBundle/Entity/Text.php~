<?php
namespace LilWorks\StoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use LilWorks\StoreBundle\Util\TagSanitizer;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="lilworks_text")
 * @ORM\HasLifecycleCallbacks()
 */
class Text
{
    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if($this->isContent == 1){
            $sanitizer = new TagSanitizer();
            $this->tag = $sanitizer->sanitize($this->getName());
        }

        $this->updatedAt= new \DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LilWorks\StoreBundle\Entity\TextBackup", mappedBy="originalText",cascade={"persist","remove"} )
     * @ORM\OrderBy({ "updatedAt" = "DESC"})
     */
    private $backups;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string",length=255,nullable=true)
     */
    private $tag;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255,nullable=false)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string",length=255,nullable=true)
     */
    private $title;
    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text",nullable=true)
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
     * @var boolean
     *
     * @ORM\Column(name="exportInBase", type="boolean",nullable=true)
     */
    private $exportInBase;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isContent", type="boolean",nullable=true)
     */
    private $isContent;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->backups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Text
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Text
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
     * Set title
     *
     * @param string $title
     *
     * @return Text
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
     * @return Text
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
     * @return Text
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
     * @return Text
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
     * Set exportInBase
     *
     * @param boolean $exportInBase
     *
     * @return Text
     */
    public function setExportInBase($exportInBase)
    {
        $this->exportInBase = $exportInBase;

        return $this;
    }

    /**
     * Get exportInBase
     *
     * @return boolean
     */
    public function getExportInBase()
    {
        return $this->exportInBase;
    }

    /**
     * Set isContent
     *
     * @param boolean $isContent
     *
     * @return Text
     */
    public function setIsContent($isContent)
    {
        $this->isContent = $isContent;

        return $this;
    }

    /**
     * Get isContent
     *
     * @return boolean
     */
    public function getIsContent()
    {
        return $this->isContent;
    }

    /**
     * Add backup
     *
     * @param \LilWorks\StoreBundle\Entity\TextBackup $backup
     *
     * @return Text
     */
    public function addBackup(\LilWorks\StoreBundle\Entity\TextBackup $backup)
    {
        $this->backups[] = $backup;

        return $this;
    }

    /**
     * Remove backup
     *
     * @param \LilWorks\StoreBundle\Entity\TextBackup $backup
     */
    public function removeBackup(\LilWorks\StoreBundle\Entity\TextBackup $backup)
    {
        $this->backups->removeElement($backup);
    }

    /**
     * Get backups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBackups()
    {
        return $this->backups;
    }
}
