<?php
namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductController
{
    private $templating;
    private $em;

    public function __construct(EngineInterface $templating,\Doctrine\ORM\EntityManager $em)
    {
        $this->templating = $templating;
        $this->em = $em;
    }

    public function getProduct($idOrTag)
    {
        if(is_int($idOrTag)){
            $product = $this->em->getRepository('LilWorksStoreBundle:Product')->findOneBy(
                array(
                #    'isPublished'=>1,
                    'id'=>$idOrTag
                )
            );
        }else{
            $product = $this->em->getRepository('LilWorksStoreBundle:Product')->findOneBy(
                array(
                    #'isPublished'=>1,
                    'tag'=>$idOrTag
                )
            );
        }
        return $this->templating->renderResponse(
            'SiteBundle:Product:index.html.twig',array(
                'product'=>$product
            ))->getContent();
    }
}