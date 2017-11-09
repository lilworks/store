<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Annonce;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\AnnonceFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Annonce controller.
 *
 */
class AnnonceController extends Controller
{
    /**
     * Lists all annonce entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(AnnonceFilterType::class);

        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Annonce')
                ->createQueryBuilder('a');

            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Annonce')
                ->createQueryBuilder('a')
                ;
        }

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.annonces');

        return $this->render('LilWorksStoreBundle:Annonce:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView()
        ));
    }

    /**
     * Creates a new annonce entity.
     *
     */
    public function newAction(Request $request)
    {
        $annonce = new Annonce();
        $form = $this->createForm('LilWorks\StoreBundle\Form\AnnonceType', $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('annonce_show', array('id' => $annonce->getId()));
        }
        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.annonces');
        return $this->render('LilWorksStoreBundle:Annonce:new.html.twig', array(
            'annonce' => $annonce,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("annonce", options={"mapping": {"annonce_id"   : "id"}})
     */
    public function showAction(Annonce $annonce)
    {

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');

        $this->get('store.setSeo')->setTitle('storebundle.title.show',array('%name%'=>$annonce->getName()),'storebundle.prefix.annonces');
        return $this->render('LilWorksStoreBundle:Annonce:show.html.twig', array(
            'annonce' => $annonce
        ));
    }

    /**
     * @ParamConverter("annonce", options={"mapping": {"annonce_id"   : "id"}})
     */
    public function editAction(Request $request, Annonce $annonce)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\AnnonceType', $annonce);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('annonce_edit', array('id' => $annonce->getId()));
        }


        $this->get('store.setSeo')->setTitle('storebundle.title.edit',array('%name%'=>$annonce->getName()),'storebundle.prefix.annonces');
        return $this->render('LilWorksStoreBundle:Annonce:edit.html.twig', array(
            'annonce' => $annonce,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("annonce", options={"mapping": {"annonce_id"   : "id"}})
     */
    public function deleteAction(Request $request,Annonce $annonce)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($annonce);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('annonce_index');
        } else {
            return $this->redirect($referer);
        }

    }

}
