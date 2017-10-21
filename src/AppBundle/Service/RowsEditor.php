<?php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class RowsEditor implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    protected $requestStack;
    protected $templating;
    protected $em;
    protected $entityName;
    protected $actions;
    protected $failed;


    public function __construct(ContainerInterface $container,$templating,\Doctrine\ORM\EntityManager $em,RequestStack $requestStack){
        $this->templating = $templating;
        $this->container = $container;
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->actions = array();

    }

    public function setActions($entityName,$actions){
        $this->entityName = $entityName;
        $this->actions = $actions;
        return $this;
    }
    public function getActions(){
        return $this->templating->render("AppBundle:RowsEditor:actions.html.twig", array(
            "actions"=>$this->actions
        ));
    }

    public function getRowCheckbox($id){
        return $this->templating->render("AppBundle:RowsEditor:checkbox.html.twig", array(
            "id"=>$id
        ));
    }
    public function doTheJob()
    {
        $this->failed = array();
        $request = $this->requestStack->getCurrentRequest();
        $em = $this->em->getRepository($this->entityName);

        $checkedValues = $request->get('checkedValues');
        $action = $request->get('action');
        $col = $request->get('col');


        foreach ($checkedValues as $id){
            $entity = $em->find($id);


            if($action == 'setBoolean'){

                $function = "set".ucfirst($col);
                $entity->$function(1);

            }elseif($action == 'unSetBoolean'){

                $function = "set".ucfirst($col);
                $entity->$function(0);

            }elseif ($action == 'delete'){
                $flag = null;

                if( count($this->actions["delete"]) > 0 ){
                    foreach ($this->actions["delete"] as $flagger){
                        $function = "get".ucfirst($flagger[0]);
                        if(!$this->doComparison(count($entity->$function()),$flagger[1],$flagger[2])){
                            $flag = true;
                            array_push($this->failed,$id);
                        }
                    }
                }

                if(is_null($flag)){
                    var_dump("DELETE");
                   $this->em->remove($entity);
                }
            }elseif ($action == 'empty'){

                $flag = null;

                if( count($this->actions["empty"]) > 0 ){
                    foreach ($this->actions["empty"] as $flagger){
                        $function = "get".ucfirst($flagger[1]);
                        if(!$this->doComparison(count($entity->$function()),$flagger[2],$flagger[3])){
                            $flag = true;
                            array_push($this->failed,$id);
                        }
                    }
                }

                if(is_null($flag)){

                    $function = "get" . ucfirst($flagger[0]);
                    foreach($entity->$function() as $child){
                        $child->removeCategory($entity);
                        $this->em->persist($child);
                    }
                }

            }

            if($action != 'delete')
                $this->em->persist($entity);

        }

        $this->em->flush();

        $response = new Response(json_encode(array($checkedValues,$this->failed)));
        return $response->getContent();
    }

    private function doComparison($a, $operator, $b)
    {
        switch ($operator) {
            case '<':  return ($a <  $b); break;
            case '<=': return ($a <= $b); break;
            case '=':  return ($a == $b); break; // SQL way
            case '==': return ($a == $b); break;
            case '!=': return ($a != $b); break;
            case '>=': return ($a >= $b); break;
            case '>':  return ($a >  $b); break;
        }

        throw new Exception("The {$operator} operator does not exists", 1);
    }
}