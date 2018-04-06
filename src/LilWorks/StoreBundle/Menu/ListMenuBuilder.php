<?php
namespace LilWorks\StoreBundle\Menu;

use Knp\Menu\FactoryInterface;

class ListMenuBuilder
{
    private $factory;
    private $options;
    private $config = array(


        'ICO_SHOW'=>'fa fa-eye',
        'ICO_EDIT'=>'fa fa-pencil',
        'ICO_DELETE'=>'fa fa-trash',
        'ICO_PDF'=>'fa fa-file-pdf-o',
        'ICO_EMPTY'=>'fa fa-share-square-o',
        'ICO_RESPOND'=>'fa fa-reply',
        'ICO_DOWNLOAD'=>'fa fa-download',
        'ICO_BACKUP'=>'fa fa-floppy-o',
        'ICO_DEVISTOFACTURE'=>'fa fa-sign-language',
        'ICO_POPULATE'=>'fa fa-plus-square',
        'ICO_POPULATE_ONLINE'=>'fa fa-plus-square',
        'ICO_POPULATE_OFFLINE'=>'fa fa-plus-square',

        'BTN_GENERAL'=>'btn btn-sm',
        'BTN_SHOW'=>'btn-info',
        'BTN_EDIT'=>'btn-primary',
        'BTN_DELETE'=>'btn-danger btn-delete',
        'BTN_PDF'=>'btn-primary',
        'BTN_EMPTY'=>'btn-warning  btn-empty',
        'BTN_RESPOND'=>'btn-primary',
        'BTN_DOWNLOAD'=>'btn-info',
        'BTN_BACKUP'=>'btn-success',
        'BTN_DEVISTOFACTURE'=>'btn-info',
        'BTN_POPULATE'=>'btn-primary',
        'BTN_POPULATE_ONLINE'=>'btn-primary',
        'BTN_POPULATE_OFFLINE'=>'btn-primary',

    );


    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createListMenu(array $options)
    {

        $target = $options['target'];
        $this->options = $options;
        $menu = $this->factory->createItem('root' ,array('childrenAttributes' => array('class' => "")));
        $menu->setExtra('translation_domain', 'LilWorksStoreBundle');

        $toAction = $menu->addChild($options['results']->getId());

        $this->$target($options["results"],$toAction);




        return $menu;


    }
    public function orderproductreturn($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('pdf',$menu,$entity->getId());

        if(!$entity->getIsArchived())
            $this->setAction('delete',$menu,$entity->getId());

        return $menu;
    }
    public function session($entity,$menu){
        $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function conversation($entity,$menu){

        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('respond',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function subscriber($entity,$menu){
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function text($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('backup',$menu,$entity->getId());
        if($entity->getIsContent() == 1){
            $this->setAction('delete',$menu,$entity->getId());
        }
        return $menu;
    }
    public function annonce($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function user($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        if(!$entity->getCustomer())
            $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function brand($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        if(count($entity->getProducts()) == 0)
            $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function docfile($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('download',$menu,$entity->getId());
        if(count($entity->getProducts()) == 0)
            $this->setAction('delete',$menu,$entity->getId());
        else
            $this->setAction('empty',$menu,$entity->getId());
        return $menu;
    }
    public function tag($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('populate',$menu,$entity->getId());

        if(count($entity->getProducts()) == 0){
            $this->setAction('delete',$menu,$entity->getId());
        }else{
            $this->setAction('empty',$menu,$entity->getId());
        }
        return $menu;
    }
    public function product($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        if(
            $entity->getOrdersProducts() && count($entity->getOrdersProducts()) == 0 &&
            $entity->getDepositSale() && count($entity->getDepositSale()) == 0)
            $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function category($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        if(count($entity->getProducts()) == 0 && count($entity->getSupercategoriesCategories()) == 0)
            $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function warranty($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('populate_offline',$menu,$entity->getId());
        $this->setAction('populate_online',$menu,$entity->getId());
        if(
            count($entity->getProductsOffline()) == 0 &&
            count($entity->getProductsOnline()) == 0 &&
            count($entity->getOrdersProducts()) == 0
        )
            $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function tax($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('populate_online',$menu,$entity->getId());
        $this->setAction('populate_offline',$menu,$entity->getId());
        if(
            count($entity->getProductsOffline()) == 0 &&
            count($entity->getProductsOnline()) == 0 &&
            count($entity->getOrdersProducts()) == 0
        )
            $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    // SuperCategory
    public function supercategory($entity,$menu){

        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());


        if(count($entity->getSupercategoriesCategories()) == 0)
            $this->setAction('delete',$menu,$entity->getId());
        else
            $this->setAction('empty',$menu,$entity->getId());

        return $menu;
    }
    public function customer($entity,$menu){
            $this->setAction('show',$menu,$entity->getId());
            $this->setAction('edit',$menu,$entity->getId());
            if(count($entity->getOrders()) == 0 && count($entity->getDepositSales()) == 0 && count($entity->getCoupons()) == 0)
                $this->setAction('delete',$menu,$entity->getId());
        return $menu;
    }
    public function order($entity,$menu){
            $this->setAction('show',$menu,$entity->getId());
            $this->setAction('pdf',$menu,$entity->getId());
            $this->setAction('edit',$menu,$entity->getId());
            $this->setAction('delete',$menu,$entity->getId());

        if($entity->getOrderType()->getTag()=="DEVIS"){
            $this->setAction('devistofacture', $menu, $entity->getId());
        }

        return $menu;
    }
    public function depositsale($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('pdf',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('delete',$menu,$entity->getId());

        return $menu;
    }
    public function coupon($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('pdf',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());

        if(count($entity->getOrdersPaymentMethods()) == 0)
            $this->setAction('delete',$menu,$entity->getId());

        return $menu;
    }

    public function country($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        if(count($entity->getAddresses()) == 0){
            $this->setAction('delete',$menu,$entity->getId());
        }
        return $menu;
    }
    public function paymentmethod($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        if(count($entity->getOrdersPaymentMethods()) ==0 ){
            $this->setAction('delete',$menu,$entity->getId());
        }
        return $menu;
    }
    public function shippingmethod($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        $this->setAction('populate',$menu,$entity->getId());
        if(
            count($entity->getShippingmethodsCountries())==0 &&
            count($entity->getOrdersRealShippingMethods())==0 &&
            count($entity->getProducts())==0
        ){
            $this->setAction('delete',$menu,$entity->getId());
        }
        return $menu;
    }
    public  function setAction($action, $menu,$id,$route=null,$routeParamName=null,$keyForTranslate=null){

        if(!$keyForTranslate)
            $keyForTranslate = 'storebundle.'.$action;
        if(!$routeParamName)
            $routeParamName = strtolower($this->options['target'].'_id');
        if(!$route)
            $route = strtolower($this->options['target'] . '_' . $action);

        $action = strtoupper($action);


        $class = $this->config["BTN_GENERAL"] . " " .$this->config["BTN_".$action];

        $menu->addChild($keyForTranslate, array(
            'attributes'=>array('class'=>'list-inline-item'),
            'linkAttributes'=>array(
                'class'=>$class,
                'i'=> $this->config["ICO_".$action]
            ),
            'route' => $route,
            'routeParameters'=>array( $routeParamName => $id)
        ));



    }



};