<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Datatables\SuperCategoryDatatable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class DefaultController extends Controller
{


    public function indexAction()
    {

        $overviewProduct = $this->container->get('lilworks.store.overview')
            ->init("LilWorksStoreBundle:Product",array('name'=>'products' , 'all','published'=>'isPublished'))
            ->getOverview();

        $overviewOrder = $this->container->get('lilworks.store.overview')
            ->init("LilWorksStoreBundle:Order",array('name'=>'orders' ,'all'))
            ->getOverview();

        $overviewCategory = $this->container->get('lilworks.store.overview')
            ->init("LilWorksStoreBundle:Category",array('name'=>'categories' ,'all','published'=>'isPublished'))
            ->getOverview();

        $overviewBrand = $this->container->get('lilworks.store.overview')
            ->init("LilWorksStoreBundle:Brand",array('name'=>'brands' ,'all'))
            ->getOverview();


        return $this->render('LilWorksStoreBundle:Default:index.html.twig',array(
            'overviews'=>array($overviewProduct,$overviewOrder,$overviewCategory,$overviewBrand)

        ));
    }

}
