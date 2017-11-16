<?php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class SimpleLiveEditor implements ContainerAwareInterface
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

    public function submitCol($formType,$entityName,$colName,$rowId){
        $entity = $this->em->getRepository($entityName)->find($rowId);

        $form = $this->container->get('form.factory')->create($formType,$entity);
        $form->handleRequest($this->requestStack->getCurrentRequest());


        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($entity);
            $this->em->flush();

        }else{
            return new RequestStack("NO CHANGE");
        }

        return new Response(
            $this->getCol($formType,$entityName,$colName,$entity)
        );
    }

    public function getCol($formType,$entityName,$colName,$entity){

        $form = $this->container->get('form.factory')->create($formType,$entity);

        $function = "get" . ucfirst($colName) ;
        $currentValue = $entity->$function();

        $metadata = $this->em->getClassMetadata($entityName);

        $colMetadata = $metadata->fieldMappings[$colName];





        return $this->templating->render("AppBundle:SimpleLiveEditor:col.html.twig", array(
            "form"=>$form->createView(),
            "currentValue"=>$currentValue,
            "colName"=>$colName,
            "rowId"=>$entity->getId(),
            "formType"=>$formType,
            "entityName"=>$entityName,
            "colMetadata"=>$colMetadata,
            "choicesOptions"=>$form->get($colName)->getConfig()->getOption('choices')
        ));
    }
} 