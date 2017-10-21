<?php

namespace LilWorks\StoreBundle\Controller;

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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.warranty.index'));


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
            $em->persist($warranty);
            $em->flush();

            return $this->redirectToRoute('warranty_show', array('id' => $warranty->getId()));
        }


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.warranty.new'));

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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.warranty.show %name%',array('%name%'=>$warranty->getName())));

        return $this->render('LilWorksStoreBundle:Warranty:show.html.twig', array(
            'warranty' => $warranty,
        ));
    }

    /**
     * @ParamConverter("warranty", options={"mapping": {"warranty_id"   : "id"}})
     */
    public function editAction(Request $request, Warranty $warranty)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\WarrantyType', $warranty);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('warranty_edit', array('id' => $warranty->getId()));
        }


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.warranty.edit %name%',array('%name%'=>$warranty->getName())));


        return $this->render('LilWorksStoreBundle:Warranty:edit.html.twig', array(
            'warranty' => $warranty,
            'form' => $editForm->createView(),
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

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('warranty_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
