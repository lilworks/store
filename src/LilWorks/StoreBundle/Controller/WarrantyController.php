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
            $em->flush();

            return $this->redirectToRoute('warranty_show', array('warranty_id' => $warranty->getId()));
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
            $em->flush();

            return $this->redirectToRoute('warranty_edit', array('warranty_id' => $warranty->getId()));

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
