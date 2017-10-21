<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\OrderStep;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;
use LilWorks\StoreBundle\Filter\OrderStepFilterType;

/**
 * OrderStep controller.
 *
 */
class OrderStepController extends Controller
{
    /**
     * Lists all orderStep entities.
     *
     */
    public function indexAction(Request $request)
    {
        

            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:OrderStep')
                ->createQueryBuilder('p')
                ;
        
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.orderstep.index'));

        return $this->render('LilWorksStoreBundle:OrderStep:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor
            
        ));
    }

    /**
     * Creates a new orderStep entity.
     *
     */
    public function newAction(Request $request)
    {
        $orderStep = new OrderStep();
        $form = $this->createForm('LilWorks\StoreBundle\Form\OrderStepType', $orderStep);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderStep);
            $em->flush();

            return $this->redirectToRoute('orderstep_show', array('id' => $orderStep->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.orderstep.new'));

        return $this->render('LilWorksStoreBundle:OrderStep:new.html.twig', array(
            'orderStep' => $orderStep,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a orderStep entity.
     *
     */
    public function showAction(OrderStep $orderStep)
    {

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.orderstep.show %name%',array('%name%'=>$orderStep->getName())));

        return $this->render('LilWorksStoreBundle:OrderStep:show.html.twig', array(
            'orderStep' => $orderStep
        ));
    }

    /**
     * Displays a form to edit an existing orderStep entity.
     *
     */
    public function editAction(Request $request, OrderStep $orderStep)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\OrderStepType', $orderStep);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orderstep_edit', array('id' => $orderStep->getId()));
        }


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.orderstep.edit %name%',array('%name%'=>$orderStep->getName())));


        return $this->render('LilWorksStoreBundle:OrderStep:edit.html.twig', array(
            'orderStep' => $orderStep,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a orderStep entity.
     *
     */
    public function deleteAction(Request $request,OrderStep $orderStep)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($orderStep);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('orderstep_index');
        } else {
            return $this->redirectToRoute($referer);
        }


    }

}
