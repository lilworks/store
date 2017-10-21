<?php
namespace LilWorks\StoreBundle\Service;


class Overview
{
    private $em;
    private $templating;

    public $entityName;
    public $outputs;

    public function __construct(\Doctrine\ORM\EntityManager $em ,$templating)
    {
        $this->em = $em;
        $this->templating = $templating;
    }


    public function init($entityName , $options){

        $this->overviewName = $options["name"];
        $this->outputs = array();
        $all = count($this->em->getRepository($entityName)->findAll());
        $this->outputs["all"]=$all;

        if(isset($options["published"])){
            $function = 'findBy' . ucfirst($options["published"]);
            $allPublished = count( $this->em->getRepository($entityName)->$function(1) );
            $this->outputs["allpublished"]=$allPublished;
        }


        return $this;
    }

    public function getOverview()
    {
        return $this->templating->render('LilWorksStoreBundle:Service:overview.html.twig', array(
            "outputs"=>$this->outputs,
            "overviewName"=>$this->overviewName
        ));
    }
}