<?php
namespace LilWorks\StoreBundle\EventListener;

use AppBundle\Event\ConfigureStoreMenuEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ConfigureStoreMenuListener
{
    private $em;
    private $requestStack;
    private $logger;
    private $target;
    private $options;
    private $config = array(

        'ICO_INDEX'=>'fa fa-list',
        'ICO_SHOW'=>'fa fa-eye',
        'ICO_NEW'=>'fa fa-plus',
        'ICO_EDIT'=>'fa fa-pencil',
        'ICO_DELETE'=>'fa fa-trash',
        'ICO_PDF'=>'fa fa-file-pdf',


        'BTN_GENERAL'=>'btn btn-sm',
        'BTN_INDEX'=>'btn-info',
        'BTN_SHOW'=>'btn-info',
        'BTN_NEW'=>'btn-primary',
        'BTN_EDIT'=>'btn-primary',
        'BTN_DELETE'=>'btn-danger btn-delete',
        'BTN_PDF'=>'btn-primary',


    );

    public function __construct(\Doctrine\ORM\EntityManager $em,RequestStack $requestStack,LoggerInterface $logger){
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }
    /**
     * @param \AppBundle\Event\ConfigureStoreMenuEvent $event
     */
    public function onMenuConfigure( ConfigureStoreMenuEvent $event)
    {
        #$this->get('logger')->info("LOG:onMenuConfigure:LOG");
        #$this->get('logger')->info(var_export($event, true));
        $fc = $event->getTarget();
        $this->target = $event->getTarget();
        $this->options = $event->getOptions();
        $this->$fc($event->getMenu(),$event->getId(),$event->getOptions());



    }





    public function  setAction($action,$entity=null,$menu,$menuName = null,$routeName=null,$routeParam=null){


        if(!$menuName)
            $menuName='storebundle.menu.'.$this->target.'.'.$action;
        if(!$routeName)
            $routeName=$this->target.'_'.$action;
        if(!$routeParam && $entity )
            $routeParam[$this->target.'_id'] = $entity->getId();


        $actionMenu = $menu->addChild($menuName, array(
            'attributes'=>$this->options['curr'],
            'route' => $routeName,
            'routeParameters' =>$routeParam
        ));

        if(isset($options['curr']))
            $actionMenu->setAttributes($this->options['curr']);
        if(isset($options['link']))
            $actionMenu->setLinkAttributes($this->options['link']);

        if(isset($this->config['ICO_'.strtoupper($action)])){
            $actionMenu->setAttribute('i',$this->config['ICO_'.strtoupper($action)]);
        }


        if($this->options["context"]=="content" && isset($this->config['BTN_'.strtoupper($action)])){
            $actionMenu->setLinkAttribute('class',$this->config['BTN_GENERAL'] . ' ' . $this->config['BTN_'.strtoupper($action)]);
        }

    }


    public function syncro($menu,$id){


        $this->setAction('index',null,$menu);


    }
    public function annonce($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Annonce";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function text($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Text";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function shippingmethod($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:ShippingMethod";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function country($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Country";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function tax($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Tax";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function warranty($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Warranty";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function paymentmethod($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:PaymentMethod";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function docfile($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Docfile";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function tag($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Tag";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }

    public function category($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Category";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);

            if (count($entity->getProducts()) == 0 && !$entity->getIsPublished() )
                $this->setAction('delete', $entity, $menu);
        }

    }
    public function supercategory($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:SuperCategory";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);



            if (count($entity->getSuperCategoriesCategories()) == 0 && !$entity->getIsPublished())
                $this->setAction('delete', $entity, $menu);
        }

    }
    public function brand($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Brand";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);

            if (count($entity->getProducts()) == 0 && !$entity->getIsPublished())
                $this->setAction('delete', $entity, $menu);
        }

    }
    public function product($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Product";
            $entity = $this->em->getRepository($entityName)->find($id);


            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);

            if (count($entity->getOrdersProducts()) == 0 and !$entity->getDepositSale())
                $this->setAction('delete', $entity, $menu);
        }
    }

    public function coupon($menu,$id,$options){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Coupon";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);

            #if($lastStep != "DONE" && $lastStep != "PAYED")
            $this->setAction('delete', $entity, $menu);
        }
    }

    public function order($menu,$id,$options){

        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {

            $entityName = "LilWorksStoreBundle:Order";
            $entity = $this->em->getRepository($entityName)->find($id);
            $lastStep = $this->em->getRepository($entityName)->getLastStep($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);

            if ($lastStep != "DONE" && $lastStep != "PAYED")
                $this->setAction('delete', $entity, $menu);
        }
    }
    public function depositSale($menu,$id,$options){


        $this->setAction('index',null,$menu);

        if($id) {
            $entityName = "LilWorksStoreBundle:DepositSale";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }
    }
    public function session($menu,$id,$options){


        $this->setAction('index',null,$menu);

        if($id) {
            $entityName = "LilWorksStoreBundle:Session";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }
    }


    public function user($menu,$id,$options){

        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "AppBundle:User";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);

            if (!$entity->getCustomer())
                $this->setAction('delete', $entity, $menu);
        }

    }

    public function customer($menu,$id,$options){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);

        if($id){
            $entityName = "LilWorksStoreBundle:Customer";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show',$entity,$menu);
            $this->setAction('edit',$entity,$menu);

            if(count($entity->getOrders()) == 0 and count($entity->getCoupons()) == 0 and count($entity->getDepositSales()) == 0 )
                $this->setAction('delete',$entity,$menu);
        }


    }



}