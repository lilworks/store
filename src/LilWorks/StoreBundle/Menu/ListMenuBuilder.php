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
        'ICO_PDF'=>'fa fa-file-pdf',

        'BTN_GENERAL'=>'btn btn-sm',
        'BTN_SHOW'=>'btn-info',
        'BTN_EDIT'=>'btn-primary',
        'BTN_DELETE'=>'btn-danger btn-delete',
        'BTN_PDF'=>'btn-primary',


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
        $menu = $this->factory->createItem('root' ,array('childrenAttributes' => array('class' => "list-inline")));
        $menu->setExtra('translation_domain', 'LilWorksStoreBundle');

        $toAction = $menu->addChild($options['results']->getId());

        $this->$target($options["results"],$toAction);




        return $menu;


    }

    public function session($entity,$menu){
        $this->setAction('delete',$menu,$entity->getId());
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

    public function product($entity,$menu){
        $this->setAction('show',$menu,$entity->getId());
        $this->setAction('edit',$menu,$entity->getId());
        if(count($entity->getOrdersProductS()) == 0 && count($entity->getDepositSale()) == 0)
            $this->setAction('delete',$menu,$entity->getId());
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
    public  function setAction($action, $menu,$id,$route=null,$routeParamName=null,$keyForTranslate=null){

        if(!$keyForTranslate)
            $keyForTranslate = 'lilworks.storebundle.menu.'.$action;
        if(!$routeParamName)
            $routeParamName = $this->options['target'].'_id';
        if(!$route)
            $route = $this->options['target'] . '_' . $action;

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