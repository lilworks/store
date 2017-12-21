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
        'ICO_EMPTY'=>'fa fa-share-square-o',
        'ICO_PDF'=>'fa fa-file-pdf-o',
        'ICO_IMPORT'=>'fa fa-upload',
        'ICO_EXPORT'=>'fa fa-download',
        'ICO_CLEAN'=>'fa fa-magic',
        'ICO_DOWNLOAD'=>'fa fa-download',
        'ICO_BACKUP'=>'fa fa-floppy-o',
        'ICO_DEVISTOFACTURE'=>'fa fa-sign-language',
        'ICO_POPULATE'=>'fa fa-plus-square',
        'ICO_POPULATE_ONLINE'=>'fa fa-plus-square',
        'ICO_POPULATE_OFFLINE'=>'fa fa-plus-square',


        'BTN_GENERAL'=>'btn btn-sm',
        'BTN_INDEX'=>'btn-info',
        'BTN_SHOW'=>'btn-info',
        'BTN_NEW'=>'btn-primary',
        'BTN_EDIT'=>'btn-primary',
        'BTN_DELETE'=>'btn-danger btn-delete',
        'BTN_EMPTY'=>'btn-warning btn-empty',
        'BTN_PDF'=>'btn-primary',
        'BTN_IMPORT'=>'btn-primary',
        'BTN_EXPORT'=>'btn-primary',
        'BTN_CLEAN'=>'btn-warning',
        'BTN_DOWNLOAD'=>'btn-info',
        'BTN_BACKUP'=>'btn-success',
        'BTN_DEVISTOFACTURE'=>'btn-info',
        'BTN_POPULATE'=>'btn-primary',
        'BTN_POPULATE_ONLINE'=>'btn-primary',
        'BTN_POPULATE_OFFLINE'=>'btn-primary',


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
            $menuName=strtolower('storebundle.menu.'.$this->target.'.'.$action);
        if(!$routeName)
            $routeName= strtolower($this->target.'_'.$action);
        if(!$routeParam && $entity )
            $routeParam[strtolower($this->target.'_id')] = $entity->getId();


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

    public function orderproductreturn($menu,$id){


        $this->setAction('index',null,$menu);

        if($id) {
            $entityName = "LilWorksStoreBundle:OrderProductReturn";
            $entity = $this->em->getRepository($entityName)->find($id);
/*
            $orderId = $entity->getOrderProduct()->getOrder()->getId();
            $menu->addChild($menu, array(
                'attributes'=>$this->options['curr'],
                'route' => "order_show",
                'routeParameters' =>array('order_id'=>$orderId)
            ));
*/
  #          $this->setAction('new',null,$menu);
  #          $this->setAction('show', $entity, $menu);
  #          $this->setAction('edit', $entity, $menu);
  #          $this->setAction('delete', $entity, $menu);


        }

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
            $this->setAction('backup', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }

    }
    public function subscriber($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('import',null,$menu);
        $this->setAction('export',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Subscriber";
            $entity = $this->em->getRepository($entityName)->find($id);

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
            $this->setAction('populate', $entity, $menu);

            if(
                count($entity->getProducts())==0 &&
                count($entity->getShippingmethodsCountries())==0 &&
                count($entity->getOrdersRealShippingMethods())==0
            ){
            $this->setAction('delete', $entity, $menu);
            }
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

            $this->setAction('populate_online', $entity, $menu);
            $this->setAction('populate_offline', $entity, $menu);



            if(
                count($entity->getProductsOnline())==0 &&
                count($entity->getProductsOffline())==0 &&
                count($entity->getOrdersProducts())==0
            ){
                $this->setAction('delete', $entity, $menu);
            }
        }

    }
    public function warranty($menu,$id){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);
        if($id) {
            $entityName = "LilWorksStoreBundle:Warranty";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('populate_online', $entity, $menu);
            $this->setAction('populate_offline', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('edit', $entity, $menu);

            if(count($entity->getProductsOnline())==0 && count($entity->getProductsOffline())==0 && count($entity->getOrdersProducts())==0)
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

            if(count($entity->getOrdersPaymentMethods())==0)
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
            $this->setAction('download', $entity, $menu);

            if(count($entity->getProducts())==0)
               $this->setAction('delete', $entity, $menu);
            else
               $this->setAction('empty', $entity, $menu);
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
            if(count($entity->getProducts())==0)
                $this->setAction('delete', $entity, $menu);
            else
                $this->setAction('empty', $entity, $menu);
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
            $this->setAction('pdf', $entity, $menu);

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

            if($entity->getOrderType()->getTag()=="DEVIS"){
                $this->setAction('devistofacture', $entity, $menu);
            }

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('pdf', $entity, $menu);

            if ($lastStep != "DONE" && $lastStep != "PAYED")
                $this->setAction('delete', $entity, $menu);
        }
    }
    public function depositSale($menu,$id,$options){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);

        if($id) {
            $entityName = "LilWorksStoreBundle:DepositSale";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('pdf', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }
    }

    public function conversation($menu,$id,$options){


        $this->setAction('index',null,$menu);
        $this->setAction('new',null,$menu);

        if($id) {
            $entityName = "LilWorksStoreBundle:Conversation";
            $entity = $this->em->getRepository($entityName)->find($id);

            $this->setAction('show', $entity, $menu);
            $this->setAction('edit', $entity, $menu);
            $this->setAction('delete', $entity, $menu);
        }
    }

    public function session($menu,$id,$options){


        $this->setAction('index',null,$menu);
        $this->setAction('clean',null,$menu);

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