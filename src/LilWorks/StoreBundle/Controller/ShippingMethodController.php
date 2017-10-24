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
        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.shippingmethod.index'));

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
            foreach( $shippingMethod->getProducts() as $p){
                if(!$p->getShippingMethods()->contains($shippingMethod)){
                    $p->addShippingMethod($shippingMethod);
                    $shippingMethod->addProduct($p);
                    $em->persist($p);
                }
            }
            $em->persist($shippingMethod);
            $em->flush();
            return $this->redirectToRoute('shippingmethod_show', array('shippingmethod_id' => $shippingMethod->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.shippingmethod.new'));


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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.shippingmethod.show %name%',array('%name%'=>$shippingMethod->getName())));

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


        $editForm = $this->createForm('LilWorks\StoreBundle\Form\ShippingMethodType', $shippingMethod);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
            return $this->redirectToRoute('shippingmethod_edit', array('shippingmethod_id' => $shippingMethod->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.shippingmethod.edit %name%',array('%name%'=>$shippingMethod->getName())));



        return $this->render('LilWorksStoreBundle:ShippingMethod:edit.html.twig', array(
            'shippingMethod' => $shippingMethod,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @ParamConverter("shippingMethod", options={"mapping": {"shippingmethod_id"   : "id"}})
     */
    public function deleteAction(Request $request,ShippingMethod $shippingMethod)
    {
        $em = $this->getDoctrine()->getManager();

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
