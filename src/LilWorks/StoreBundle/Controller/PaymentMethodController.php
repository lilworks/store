<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\PaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Paymentmethod controller.
 *
 */
class PaymentMethodController extends Controller
{
    /**
     * Lists all paymentMethod entities.
     *
     */
    public function indexAction(Request $request)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from('LilWorksStoreBundle:PaymentMethod','p')
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.paymentmethods');

        return $this->render('LilWorksStoreBundle:PaymentMethod:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor
        ));
    }

    /**
     * Creates a new paymentMethod entity.
     *
     */
    public function newAction(Request $request)
    {
        $paymentMethod = new Paymentmethod();
        $form = $this->createForm('LilWorks\StoreBundle\Form\PaymentMethodType', $paymentMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($paymentMethod);
            $em->flush();

            return $this->redirectToRoute('paymentmethod_show', array('paymentmethod_id' => $paymentMethod->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.paymentmethods');

        return $this->render('LilWorksStoreBundle:PaymentMethod:new.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("paymentMethod", options={"mapping": {"paymentmethod_id"   : "id"}})
     */
    public function showAction(PaymentMethod $paymentMethod)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array('%name%'=>$paymentMethod->getName()),'storebundle.prefix.paymentmethods');


        return $this->render('LilWorksStoreBundle:PaymentMethod:show.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'simple_live_editor'=>$simpleLiveEditor
        ));
    }

    /**
     * @ParamConverter("paymentMethod", options={"mapping": {"paymentmethod_id"   : "id"}})
     */
    public function editAction(Request $request, PaymentMethod $paymentMethod)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\PaymentMethodType', $paymentMethod);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('paymentmethod_edit', array('paymentmethod_id' => $paymentMethod->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array('%name%'=>$paymentMethod->getName()),'storebundle.prefix.paymentmethods');


        return $this->render('LilWorksStoreBundle:PaymentMethod:edit.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("paymentMethod", options={"mapping": {"paymentmethod_id"   : "id"}})
     */
    public function deleteAction(Request $request,PaymentMethod $paymentMethod)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($paymentMethod);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('paymentmethod_index');
        } else {
            return $this->redirectToRoute($referer);
        }


    }


}
