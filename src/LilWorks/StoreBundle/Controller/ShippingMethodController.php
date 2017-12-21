<?php

namespace LilWorks\StoreBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use LilWorks\StoreBundle\Entity\ShippingMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Shippingmethod controller.
 *
 */
class ShippingMethodController extends Controller
{
    /**
     * Lists all shippingMethod entities.
     *
     */
    public function indexAction(Request $request)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');
        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('sm')
            ->from('LilWorksStoreBundle:ShippingMethod','sm')
        ;


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            array('defaultSortFieldName' => 'sm.priority', 'defaultSortDirection' => 'desc')
        );

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.shippingmethods');
        return $this->render('LilWorksStoreBundle:ShippingMethod:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor
        ));

    }

    /**
     * Creates a new shippingMethod entity.
     *
     */
    public function newAction(Request $request)
    {
        $shippingMethod = new Shippingmethod();
        $form = $this->createForm('LilWorks\StoreBundle\Form\ShippingMethodType', $shippingMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /*

             foreach( $shippingMethod->getProducts() as $p){
                if(!$p->getShippingMethods()->contains($shippingMethod)){
                    $p->addShippingMethod($shippingMethod);
                    $shippingMethod->addProduct($p);
                    $em->persist($p);
                }
            }
            $em->persist($shippingMethod);
            */
            foreach( $shippingMethod->getTriggers() as $t){
                $t->setShippingMethod($shippingMethod);
                $shippingMethod->addTrigger($t);
                $em->persist($t);
            }

            $em->persist($shippingMethod);


            $this->get('store.flash')->setMessages(array(
                array('status'=>'success','message'=>'storebundle.flash.shippingmethod.created')
            ));
            $em->flush();
            return $this->redirectToRoute('shippingmethod_show', array('shippingmethod_id' => $shippingMethod->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.shippingmethods');


        return $this->render('LilWorksStoreBundle:ShippingMethod:new.html.twig', array(
            'shippingMethod' => $shippingMethod,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("shippingMethod", options={"mapping": {"shippingmethod_id"   : "id"}})
     */
    public function showAction(ShippingMethod $shippingMethod)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');


        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array('%name%'=>$shippingMethod->getName()),'storebundle.prefix.shippingmethods');

        return $this->render('LilWorksStoreBundle:ShippingMethod:show.html.twig', array(
            'shippingMethod' => $shippingMethod,
            'simple_live_editor'=>$simpleLiveEditor
        ));
    }

    /**
     * @ParamConverter("shippingMethod", options={"mapping": {"shippingmethod_id"   : "id"}})
     */
    public function editAction(Request $request, ShippingMethod $shippingMethod)
    {
        $originalProducts = new ArrayCollection();
        foreach ($shippingMethod->getProducts() as $p) {
            $originalProducts->add($p);
        }
        $originalTriggers = new ArrayCollection();
        // Create an ArrayCollection of the current shippingmethodCountry objects in the database
        foreach ($shippingMethod->getTriggers() as $t) {
            $originalTriggers->add($t);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\ShippingMethodType', $shippingMethod);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($originalTriggers as $t) {

                if (false === $shippingMethod->getTriggers()->contains($t)) {
                    $t->setShippingMethod(null);
                    $em->remove($t);
                }


            }

            foreach ($shippingMethod->getTriggers() as $triggerFromForm) {
                $triggerFromForm->setShippingMethod($shippingMethod);
                $em->persist($triggerFromForm);

            }

            foreach($originalProducts as $p){
                if( false === $shippingMethod->getProducts()->contains($p) ){
                    $p->removeShippingMethod($shippingMethod);
                    $shippingMethod->removeProduct($p);
                    $em->persist($p);
                }
            }
            foreach( $shippingMethod->getProducts() as $p){
                if(!$p->getShippingMethods()->contains($shippingMethod)){
                    $p->addShippingMethod($shippingMethod);
                    $shippingMethod->addProduct($p);
                    $em->persist($p);
                }
            }
            $em->persist($shippingMethod);
            $em->flush();
            $this->get('store.flash')->setMessages(array(
                array('status'=>'success','message'=>'storebundle.flash.shippingmethod.updated')
            ));
            return $this->redirectToRoute('shippingmethod_edit', array('shippingmethod_id' => $shippingMethod->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array('%name%'=>$shippingMethod->getName()),'storebundle.prefix.shippingmethods');


        return $this->render('LilWorksStoreBundle:ShippingMethod:edit.html.twig', array(
            'shippingMethod' => $shippingMethod,
            'form' => $editForm->createView(),
        ));
    }



    /**
     * @ParamConverter("shippingMethod", options={"mapping": {"shippingmethod_id"   : "id"}})
     */
    public function populateAction(Request $request, ShippingMethod $shippingMethod)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.populate %name%',array('%name%'=>$shippingMethod->getName()),'storebundle.prefix.shippingmethods');
        $object = array(
            'id'=>$shippingMethod->getId(),
            'entity'=>'LilWorksStoreBundle:ShippingMethod',
            'child'=>'products',
            'childEntity'=>'LilWorksStoreBundle:Product',
            'childMethod'=>'shippingMethod',
        );
        return $this->render('LilWorksStoreBundle:ShippingMethod:populate.html.twig', array(
            'shippingMethod' => $shippingMethod,
            'object'=>$object
        ));
    }

    /**
     * @ParamConverter("shippingMethod", options={"mapping": {"shippingmethod_id"   : "id"}})
     */
    public function deleteAction(Request $request,ShippingMethod $shippingMethod)
    {
        $em = $this->getDoctrine()->getManager();


        $this->get('store.flash')->setMessages(array(
            array('status'=>'success','message'=>'storebundle.flash.shippingmethod.deleted')
        ));

        $em->remove($shippingMethod);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('shippingmethod_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
