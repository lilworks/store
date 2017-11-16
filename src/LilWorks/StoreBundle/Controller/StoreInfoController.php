<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use LilWorks\StoreBundle\Entity\StoreInfo;

/**
 * StoreInfo controller.
 *
 */
class StoreInfoController extends Controller
{
    /**
     * Lists all country entities.
     *
     */
    public function indexAction(Request $request)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from('LilWorksStoreBundle:StoreInfo','p')
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.storeinfo.index'));

        return $this->render('LilWorksStoreBundle:StoreInfo:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor
        ));
    }

    /**
     * Creates a new StoreInfo entity.
     *
     */
    public function newAction(Request $request)
    {
        $storeInfo = new StoreInfo();
        $form = $this->createForm('LilWorks\StoreBundle\Form\StoreInfoType', $storeInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storeInfo);
            $em->flush();

            return $this->redirectToRoute('country_show', array('id' => $storeInfo->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.storeinfo.new'));


        return $this->render('LilWorksStoreBundle:StoreInfo:new.html.twig', array(
            'storeInfo' => $storeInfo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a StoreInfo entity.
     *
     */
    public function showAction(StoreInfo $storeInfo)
    {
        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.storeinfo.show %tag%',array('%tag%'=>$storeInfo->getTag())));


        return $this->render('LilWorksStoreBundle:StoreInfo:show.html.twig', array(
            'storeInfo' => $storeInfo,
        ));
    }

    /**
     * Displays a form to edit an existing StoreInfo entity.
     *
     */
    public function editAction(Request $request, StoreInfo $storeInfo)
    {

        $em = $this->getDoctrine()->getManager();



        $editForm = $this->createForm('LilWorks\StoreBundle\Form\StoreInfoType', $storeInfo);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {



            $em->persist($storeInfo);
            $em->flush();

            return $this->redirectToRoute('storeinfo_edit', array('id' => $storeInfo->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.storeinfo.edit %tag%',array('%tag%'=>$storeInfo->getTag())));

        return $this->render('LilWorksStoreBundle:StoreInfo:edit.html.twig', array(
            'storeInfo' => $storeInfo,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a StoreInfo entity.
     *
     */
    public function deleteAction(Request $request,StoreInfo $storeInfo)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($storeInfo);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('storeinfo_index');
        } else {
            return $this->redirectToRoute($referer);
        }


    }
}
