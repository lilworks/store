<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\ShippingMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
            $em->persist($shippingMethod);
            $em->flush();

            return $this->redirectToRoute('shippingmethod_show', array('id' => $shippingMethod->getId()));
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
     * Finds and displays a shippingMethod entity.
     *
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
     * Displays a form to edit an existing shippingMethod entity.
     *
     */
    public function editAction(Request $request, ShippingMethod $shippingMethod)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\ShippingMethodType', $shippingMethod);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shippingmethod_edit', array('id' => $shippingMethod->getId()));
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
     * Deletes a shippingMethod entity.
     *
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
            return $this->redirectToRoute($referer);
        }


    }

}
