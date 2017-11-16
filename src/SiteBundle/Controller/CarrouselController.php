<?php
namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class CarrouselController
{
    private $templating;
    private $em;

    public function __construct(EngineInterface $templating,\Doctrine\ORM\EntityManager $em)
    {
        $this->templating = $templating;
        $this->em = $em;
    }

    public function getCarrousel()
    {
        $annonces = $this->em->getRepository('LilWorksStoreBundle:Annonce')->findBy(
            array('isPublished'=>1),
            array('pos'=>'asc')
        );

        return $this->templating->renderResponse(
            'SiteBundle:Carrousel:index.html.twig',array(
                'annonces'=>$annonces
            ))->getContent();
    }
}