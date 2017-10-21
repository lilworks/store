<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\TagFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Tag controller.
 *
 */
class TagController extends Controller
{
    /**
     * Lists all Tag entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(TagFilterType::class);

        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Tag')
                ->createQueryBuilder('t');
/*
            $filterBuilder
                ->leftJoin('LilWorksStoreBundle:Product','pr','WITH','pr.brand = b.id')
                ->groupBy("b.id");
*/
            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Tag')
                ->createQueryBuilder('t')
               // ->leftJoin('LilWorksStoreBundle:Product','pr','WITH','pr.brand = b.id')
               // ->groupBy("b.id")
            ;
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
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.tag.index'));

        return $this->render('LilWorksStoreBundle:Tag:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView()
        ));
    }

    /**
     * Creates a new Tag entity.
     *
     */
    public function newAction(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm('LilWorks\StoreBundle\Form\TagType', $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('tag_show', array('id' => $tag->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.tag.new'));

        return $this->render('LilWorksStoreBundle:Tag:new.html.twig', array(
            'tag' => $tag,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("tag", options={"mapping": {"tag_id"   : "id"}})
     */
    public function showAction(Tag $tag)
    {

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.tag.show %name%',array('%name%'=>$tag->getName())));

        return $this->render('LilWorksStoreBundle:Tag:show.html.twig', array(
            'tag' => $tag
        ));
    }

    /**
     * @ParamConverter("tag", options={"mapping": {"tag_id"   : "id"}})
     */
    public function editAction(Request $request, Tag $tag)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\TagType', $tag);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tag_edit', array('id' => $tag->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.tag.edit %name%',array('%name%'=>$tag->getName())));


        return $this->render('LilWorksStoreBundle:Tag:edit.html.twig', array(
            'tag' => $tag,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("tag", options={"mapping": {"tag_id"   : "id"}})
     */
    public function deleteAction(Request $request,Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($tag);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('tag_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
