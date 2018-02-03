<?php

namespace LilWorks\StoreBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use LilWorks\StoreBundle\Entity\Warranty;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Warranty controller.
 *
 */
class WarrantyController extends Controller
{
    /**
     * Lists all warranty entities.
     */
    public function indexAction(Request $request)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('w')
            ->from('LilWorksStoreBundle:Warranty','w')
        ;


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.warranties');

        return $this->render('LilWorksStoreBundle:Warranty:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor
        ));

    }

    /**
     * Creates a new warranty entity.
     *
     */
    public function newAction(Request $request)
    {
        $warranty = new Warranty();
        $form = $this->createForm('LilWorks\StoreBundle\Form\WarrantyType', $warranty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /*
            foreach( $warranty->getProductsOnline() as $p){
                if(!$p->getWarrantiesOnline()->contains($warranty)){
                    $p->addWarrantiesOnline($warranty);
                    $warranty->addProductsOnline($p);
                    $em->persist($p);
                }
            }

            foreach( $warranty->getProductsOffline() as $p){
                if(!$p->getWarrantiesOffline()->contains($warranty)) {
                    $p->addWarrantiesOffline($warranty);
                    $warranty->addProductsOffline($p);
                    $em->persist($p);
                }
            }
            */
            $em->persist($warranty);
            $em->flush();

            $this->get('store.flash')->setMessages(array(
                array('status'=>'success','message'=>'storebundle.flash.warranty.created')
            ));


            return $this->redirectToRoute('warranty_show', array('warranty_id' => $warranty->getId()));
        }


        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.warranties');

        return $this->render('LilWorksStoreBundle:Warranty:new.html.twig', array(
            'warranty' => $warranty,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("warranty", options={"mapping": {"warranty_id"   : "id"}})
     */
    public function showAction(Warranty $warranty)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array('%name%'=>$warranty->getName()),'storebundle.prefix.warranties');

        return $this->render('LilWorksStoreBundle:Warranty:show.html.twig', array(
            'warranty' => $warranty,
        ));
    }

    /**
     * @ParamConverter("warranty", options={"mapping": {"warranty_id"   : "id"}})
     */
    public function editAction(Request $request, Warranty $warranty)
    {


        $originalProductsOnline = new ArrayCollection();
        foreach ($warranty->getProductsOnline() as $p) {
            $originalProductsOnline->add($p);
        }
        $originalProductsOffline = new ArrayCollection();
        foreach ($warranty->getProductsOffline() as $p) {
            $originalProductsOffline->add($p);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\WarrantyType', $warranty);
        $editForm->handleRequest($request);



        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /*
            foreach($originalProductsOnline as $p){
                if( false === $warranty->getProductsOnline()->contains($p) ){
                    $p->removeWarrantiesOnline($warranty);
                    $warranty->removeProductsOnline($p);
                    $em->persist($p);
                }
            }
            foreach( $warranty->getProductsOnline() as $p){
                if(!$p->getWarrantiesOnline()->contains($warranty)){
                    $p->addWarrantiesOnline($warranty);
                    $warranty->addProductsOnline($p);
                    $em->persist($p);
                }
            }
            foreach($originalProductsOffline as $p){
                if( false === $warranty->getProductsOffline()->contains($p) ){
                    $p->removeWarrantiesOffline($warranty);
                    $warranty->removeProductsOffline($p);
                    $em->persist($p);
                }
            }
            foreach( $warranty->getProductsOffline() as $p){
                if(!$p->getWarrantiesOffline()->contains($warranty)) {
                    $p->addWarrantiesOffline($warranty);
                    $warranty->addProductsOffline($p);
                    $em->persist($p);
                }
            }
            */
            $em->persist($warranty);
            $em->flush();

            $this->get('store.flash')->setMessages(array(
                array('status'=>'success','message'=>'storebundle.flash.warranty.updated')
            ));

            return $this->redirectToRoute('warranty_edit', array('warranty_id' => $warranty->getId()));

        }


        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array('%name%'=>$warranty->getName()),'storebundle.prefix.warranty');

        return $this->render('LilWorksStoreBundle:Warranty:edit.html.twig', array(
            'warranty' => $warranty,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @ParamConverter("warranty", options={"mapping": {"warranty_id"   : "id"}})
     */
    public function populateOfflineAction(Request $request, Warranty $warranty)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.populate.offline %name%',array('%name%'=>$warranty->getName()),'storebundle.prefix.warranty');
        $object = array(
            'id'=>$warranty->getId(),
            'entity'=>'LilWorksStoreBundle:Warranty',
            'child'=>'productsOffline',
            'childEntity'=>'LilWorksStoreBundle:Product',
            'childMethod'=>'warrantiesOffline',
        );
        return $this->render('LilWorksStoreBundle:Warranty:populate-offline.html.twig', array(
            'warranty' => $warranty,
            'object'=>$object
        ));
    }
    /**
     * @ParamConverter("warranty", options={"mapping": {"warranty_id"   : "id"}})
     */
    public function populateOnlineAction(Request $request, Warranty $warranty)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.populate.online %name%',array('%name%'=>$warranty->getName()),'storebundle.prefix.warranty');
        $object = array(
            'id'=>$warranty->getId(),
            'entity'=>'LilWorksStoreBundle:Warranty',
            'child'=>'productsOnline',
            'childEntity'=>'LilWorksStoreBundle:Product',
            'childMethod'=>'warrantiesOnline',
        );
        return $this->render('LilWorksStoreBundle:Warranty:populate-online.html.twig', array(
            'warranty' => $warranty,
            'object'=>$object
        ));
    }


    /**
     * @ParamConverter("warranty", options={"mapping": {"warranty_id"   : "id"}})
     */
    public function deleteAction(Request $request,Warranty $warranty)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($warranty);
        $em->flush();

        $this->get('store.flash')->setMessages(array(
            array('status'=>'success','message'=>'storebundle.flash.warranty.deleted')
        ));


        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('warranty_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
