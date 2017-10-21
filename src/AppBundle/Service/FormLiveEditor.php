<?php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class FormLiveEditor implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    protected $requestStack;
    protected $templating;
    protected $em;


    public function __construct(ContainerInterface $container,$templating,\Doctrine\ORM\EntityManager $em,RequestStack $requestStack){
        $this->templating = $templating;
        $this->container = $container;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }


    public function emptyCollection($entityName,$colRef,$rowId){
        $function = "findBy" . ucfirst($colRef);
        $entities = $this->em->getRepository($entityName)->$function($rowId);
        foreach($entities as $entity){
            $this->em->remove($entity);
        }
        $this->em->flush();
        return new Response("done");
    }

    public function addCollection($formType,$entityName,$colRef,$rowId){
        $form = $this->container->get('form.factory')->create($formType,null);
        return new Response( $this->templating->render("AppBundle:FormLiveEditor:addCollection.html.twig", array(
            "form"=>$form->createView(),
        )))
            ;
    }
} 