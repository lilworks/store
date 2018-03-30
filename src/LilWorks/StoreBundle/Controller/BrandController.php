<?php
namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Brand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\BrandFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Brand controller.
 *
 */
class BrandController extends Controller
{
    /**
     * Lists all brand entities.
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(BrandFilterType::class);

        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Brand')
                ->createQueryBuilder('b');

            $filterBuilder
                ->leftJoin('LilWorksStoreBundle:Product','pr','WITH','pr.brand = b.id')
                ->groupBy("b.id");

            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Brand')
                ->createQueryBuilder('b')
                ->leftJoin('LilWorksStoreBundle:Product','pr','WITH','pr.brand = b.id')
                ->groupBy("b.id");
        }

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.brands');

        return $this->render('LilWorksStoreBundle:Brand:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView()
        ));
    }

    /**
     * Creates a new brand entity.
     *
     */
    public function newAction(Request $request)
    {
        $brand = new Brand();
        $form = $this->createForm('LilWorks\StoreBundle\Form\BrandType', $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if(count($brand->getProducts())>0){
                foreach($brand->getProducts() as $product){
                    $product->setBrand($brand);
                    $brand->addProduct($product);
                    $em->persist($product);
                }
            }

            $em->persist($brand);
            $em->flush();

            return $this->redirectToRoute('brand_show', array('brand_id' => $brand->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.brands');

        return $this->render('LilWorksStoreBundle:Brand:new.html.twig', array(
            'brand' => $brand,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("brand", options={"mapping": {"brand_id"   : "id"}})
     */
    public function showAction(Brand $brand)
    {


        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array('%name%'=>$brand->getName()),'storebundle.prefix.brands');
        return $this->render('LilWorksStoreBundle:Brand:show.html.twig', array(
            'brand' => $brand
        ));
    }

    /**
     * @ParamConverter("brand", options={"mapping": {"brand_id"   : "id"}})
     */
    public function editAction(Request $request, Brand $brand)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\BrandType', $brand);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            if(count($brand->getProducts())>0){
                $em = $this->getDoctrine()->getManager();
                foreach($brand->getProducts() as $product){
                    $product->setBrand($brand);
                    $brand->addProduct($product);
                    $em->persist($product);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('brand_edit', array('brand_id' => $brand->getId()));
        }


        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array('%name%'=>$brand->getName()),'storebundle.prefix.brands');
        return $this->render('LilWorksStoreBundle:Brand:edit.html.twig', array(
            'brand' => $brand,
            'form' => $editForm->createView()
        ));
    }
    /**
     * @ParamConverter("brand", options={"mapping": {"brand_id"   : "id"}})
     */
    public function populateAction(Request $request, Brand $brand)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.populate %name%',array('%name%'=>$brand->getName()),'storebundle.prefix.brand');
        $object = array(
            'id'=>$brand->getId(),
            'entity'=>'LilWorksStoreBundle:Brand',
            'child'=>'products',
            'childEntity'=>'LilWorksStoreBundle:Product',
            'childMethod'=>'brand',
        );
        return $this->render('LilWorksStoreBundle:Brand:populate.html.twig', array(
            'brand' => $brand,
            'object'=>$object
        ));
    }

    /**
     * @ParamConverter("brand", options={"mapping": {"brand_id"   : "id"}})
     */
    public function deleteAction(Request $request,Brand $brand)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($brand);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('brand_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
