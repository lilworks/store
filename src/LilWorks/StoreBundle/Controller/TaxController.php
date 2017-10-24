<?php

namespace LilWorks\StoreBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use LilWorks\StoreBundle\Entity\Tax;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Tax controller.
 *
 */
class TaxController extends Controller
{
    /**
     * Lists all tax entities.
     *
     */
    public function indexAction(Request $request)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from('LilWorksStoreBundle:Tax','p')
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.tax.index'));

        return $this->render('LilWorksStoreBundle:Tax:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor
        ));
    }

    /**
     * Creates a new tax entity.
     *
     */
    public function newAction(Request $request)
    {
        $tax = new Tax();
        $form = $this->createForm('LilWorks\StoreBundle\Form\TaxType', $tax);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if( count($tax->getProductsOffline())>0){
                foreach($tax->getProductsOffline() as $product){
                    if (false === $tax->getProductsOffline()->contains($tax)) {
                        die();
                    }
                    $product->addTaxOffline($tax);
                    $em->persist($product);
                }
            }

            if( count($tax->getProductsOnline())>0){
                foreach($tax->getProductsOnline() as $product){
                    if (false === $tax->getProductsOnline()->contains($tax)) {
                        die();
                    }
                    $product->addTaxOnline($tax);
                    $em->persist($product);
                }
            }
            $em->persist($tax);
            $em->flush();

            return $this->redirectToRoute('tax_show', array('id' => $tax->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.tax.new'));

        return $this->render('LilWorksStoreBundle:Tax:new.html.twig', array(
            'tax' => $tax,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("tax", options={"mapping": {"tax_id"   : "id"}})
     */
    public function showAction(Tax $tax)
    {
        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.tax.show %name%',array('%name%'=>$tax->getName())));

        return $this->render('LilWorksStoreBundle:Tax:show.html.twig', array(
            'tax' => $tax,
        ));
    }

    /**
     * @ParamConverter("tax", options={"mapping": {"tax_id"   : "id"}})
     */
    public function editAction(Request $request, Tax $tax)
    {

        $originalProductsOnline = new ArrayCollection();
        foreach ($tax->getProductsOnline() as $p) {
            $originalProductsOnline->add($p);
        }
        $originalProductsOffline = new ArrayCollection();
        foreach ($tax->getProductsOffline() as $p) {
            $originalProductsOffline->add($p);
        }
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\TaxType', $tax);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();


            foreach($originalProductsOnline as $p){
                if( false === $tax->getProductsOnline()->contains($p) ){
                    $p->removeTaxOnline($tax);
                    $tax->removeProductsOnline($p);
                    $em->persist($p);
                }
            }
            foreach( $tax->getProductsOnline() as $p){
                if(!$p->getTaxesOnline()->contains($tax)){
                    $p->addTaxOnline($tax);
                    $tax->addProductsOnline($p);
                    $em->persist($p);
                }
            }
            foreach($originalProductsOffline as $p){
                if( false === $tax->getProductsOffline()->contains($p) ){
                    $p->removeTaxOffline($tax);
                    $tax->removeProductsOffline($p);
                    $em->persist($p);
                }
            }
            foreach( $tax->getProductsOffline() as $p){
                if(!$p->getTaxesOffline()->contains($tax)) {
                    $p->addTaxOffline($tax);
                    $tax->addProductsOffline($p);
                    $em->persist($p);
                }
            }
            $em->flush();
            return $this->redirectToRoute('tax_edit', array('tax_id' => $tax->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.tax.edit %name%',array('%name%'=>$tax->getName())));

        return $this->render('LilWorksStoreBundle:Tax:edit.html.twig', array(
            'tax' => $tax,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("tax", options={"mapping": {"tax_id"   : "id"}})
     */
    public function deleteAction(Request $request,Tax $tax)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($tax);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('tax_index');
        } else {
            return $this->redirect($referer);
        }


    }



}
