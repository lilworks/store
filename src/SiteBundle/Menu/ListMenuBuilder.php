<?php
namespace SiteBundle\Menu;

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


    public function order($entity,$menu){
            $this->setAction('show',$menu,$entity->getId());
            $this->setAction('edit',$menu,$entity->getId());

        if($entity->getPayed()==0)
            $this->setAction('delete',$menu,$entity->getId());

        if($entity->getPayed()>0 && $entity->getPayed() == $entity->getTot())
            $this->setAction('pdf',$menu,$entity->getId());


        return $menu;
    }


    public  function setAction($action, $menu,$id,$route=null,$routeParamName=null,$keyForTranslate=null){

        if(!$keyForTranslate)
            $keyForTranslate = 'sitebundle.'.$action;
        if(!$routeParamName)
            $routeParamName = strtolower($this->options['target'].'_id');
        if(!$route)
            $route = strtolower('site_'.$this->options['target'] . '_' . $action);

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