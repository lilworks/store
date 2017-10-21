<?php
namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Basket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;
use LilWorks\StoreBundle\Filter\BasketFilterType;

/**
 * Basket controller.
 *
 */
class BasketController extends Controller
{
    /**
     * Lists all basket entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(BasketFilterType::class);

        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Basket')
                ->createQueryBuilder('b');


            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Basket')
                ->createQueryBuilder('b');
        }

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.basket.index'));


        return $this->render('LilWorksStoreBundle:Basket:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView()
        ));
    }

    /**
     * Creates a new basket entity.
     *
     */
    public function newAction(Request $request)
    {
        $basket = new Basket();
        $form = $this->createForm('LilWorks\StoreBundle\Form\BasketType', $basket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($basket);
            $em->flush();

            return $this->redirectToRoute('basket_show', array('id' => $basket->getId()));
        }


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.basket.new'));

        return $this->render('LilWorksStoreBundle:Basket:new.html.twig', array(
            'basket' => $basket,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a basket entity.
     *
     */
    public function showAction(Basket $basket)
    {

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.basket.show %id%',array('%id%'=>$basket->getId())));


        return $this->render('LilWorksStoreBundle:Basket:show.html.twig', array(
            'basket' => $basket
        ));
    }

    /**
     * Displays a form to edit an existing basket entity.
     *
     */
    public function editAction(Request $request, Basket $basket)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\BasketType', $basket);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('basket_edit', array('id' => $basket->getId()));
        }


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.basket.edit %id%',array('%id%'=>$basket->getId())));

        return $this->render('LilWorksStoreBundle:Basket:edit.html.twig', array(
            'basket' => $basket,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a basket entity.
     *
     */
    public function deleteAction(Request $request,Basket $basket)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($basket);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('basket_index');
        } else {
            return $this->redirectToRoute($referer);
        }


    }

}
