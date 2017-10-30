<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Subscriber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\SubscriberFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Subscriber controller.
 *
 */
class SubscriberController extends Controller
{

    /**
     * Lists all Newletter entities.
     *
     */
    public function indexAction(Request $request)
    {
        $rowsLiveEditor      = $this->get('app.rowsEditor')->setActions('LilWorksStoreBundle:Subscriber',array(
            "delete"=> [ array('products','==',0) ],
            "empty"=>[ array('products', 'products','>',0) ],
            "cols"=>array(
                "isPublished"=> array('boolean', "cond"=>array('products','>',0)),
            )
        ));


        if ($request->isXMLHttpRequest()) {
            return new Response($rowsLiveEditor->doTheJob());
        }


        $formFilter = $this->get('form.factory')->create(SubscriberFilterType::class);

        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Subscriber')
                ->createQueryBuilder('n')
            ;

            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Subscriber')
                ->createQueryBuilder('n')
            ;
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $request->query->getInt('maxItemPerPage', 10)
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.subscriber.index'));


        return $this->render('LilWorksStoreBundle:Subscriber:index.html.twig', array(
            'pagination' => $pagination,
            'formFilter'=>$formFilter->createView(),
            'rowsLiveEditor'=>$rowsLiveEditor
        ));
    }

    /**
     * Creates a new Subscriber entity.
     *
     */
    public function newAction(Request $request)
    {
        $subscriber = new Subscriber();
        $form = $this->createForm('LilWorks\StoreBundle\Form\SubscriberType', $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach($subscriber->getProducts() as $product){
                $product->addSubscriber($subscriber);
                $em->persist($product);
            }

            $em->persist($subscriber);
            $em->flush();

            return $this->redirectToRoute('subscriber_show', array('subscriber_id' => $subscriber->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.subscriber.new'));

        return $this->render('LilWorksStoreBundle:Subscriber:new.html.twig', array(
            'subscriber' => $subscriber,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("subscriber", options={"mapping": {"subscriber_id"   : "id"}})
     */
    public function showAction(Subscriber $subscriber)
    {

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.subscriber.show %name%',array('%name%'=>$subscriber->getName())));


        return $this->render('LilWorksStoreBundle:Subscriber:show.html.twig', array(
            'subscriber' => $subscriber
        ));
    }

    /**
     * @ParamConverter("subscriber", options={"mapping": {"subscriber_id"   : "id"}})
     */
    public function editAction(Request $request, Subscriber $subscriber)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\SubscriberType', $subscriber);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach($subscriber->getProducts() as $product){
                if (false === $product->getCategories()->contains($subscriber)) {
                    $product->addSubscriber($subscriber);
                    $em->persist($product);
                }
            }

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('subscriber_edit', array('subscriber_id' => $subscriber->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('subscriber edit'));

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.subscriber.edit %name%',array('%name%'=>$subscriber->getName())));



        return $this->render('LilWorksStoreBundle:Subscriber:edit.html.twig', array(
            'subscriber' => $subscriber,
            'form' => $editForm->createView()
        ));
    }
    /**
     * @ParamConverter("subscriber", options={"mapping": {"subscriber_id"   : "id"}})
     */
    public function emptyAction(Request $request, Subscriber $subscriber)
    {
        $em = $this->getDoctrine()->getManager();

        foreach($subscriber->getProducts() as $product){
            $subscriber->removeProduct($product);
            $product->removeSubscriber($subscriber);

        }
        $em->persist($subscriber);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('brand_index');
        } else {
            return $this->redirect($referer);
        }

    }
    /**
     * @ParamConverter("subscriber", options={"mapping": {"subscriber_id"   : "id"}})
     */
    public function deleteAction(Request $request,Subscriber $subscriber)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($subscriber);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('subscriber_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
