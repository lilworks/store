<?php
namespace SiteBundle\Service;



class Content
{

    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }


    public function  getText($tag)
    {

        if($text = $this->em->getRepository("LilWorksStoreBundle:Text")->findOneByTag($tag)){
            return $text;
        }
    }

}
