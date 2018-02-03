<?php

namespace LilWorks\StoreBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
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

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.tags');

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
            /*
            foreach( $tag->getProducts() as $p){
                if(!$p->getTags()->contains($tag)){
                    $p->addTag($tag);
                    $tag->addProduct($p);
                    $em->persist($p);
                }
            }
            */
            $tags = explode(',',$tag->getName());
            if(count($tags)>0){
                foreach($tags as $tagName){
                    $tag = new Tag();
                    $tag->setName($tagName);
                    $em->persist($tag);
                }
            }

            $em->flush();

            $this->get('store.flash')->setMessages(array(
                array('status'=>'success','message'=>'storebundle.flash.tag.created')
            ));

            return $this->redirectToRoute('tag_index');
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.tags');

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

        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array('%name%'=>$tag->getName()),'storebundle.prefix.tags');

        return $this->render('LilWorksStoreBundle:Tag:show.html.twig', array(
            'tag' => $tag,
        ));
    }

    /**
     * @ParamConverter("tag", options={"mapping": {"tag_id"   : "id"}})
     */
    public function editAction(Request $request, Tag $tag)
    {
        $originalProducts = new ArrayCollection();
        foreach ($tag->getProducts() as $p) {
            $originalProducts->add($p);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\TagType', $tag);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /*
            foreach($originalProducts as $p){
                if( false === $tag->getProducts()->contains($p) ){
                    $p->removeWarrantiesOnline($tag);
                    $tag->removeProduct($p);
                    $em->persist($p);
                }
            }
            foreach( $tag->getProducts() as $p){
                if(!$p->getTags()->contains($tag)){
                    $p->addTag($tag);
                    $tag->addProduct($p);
                    $em->persist($p);
                }
            }
            */
            $em->flush();

            $this->get('store.flash')->setMessages(array(
                array('status'=>'success','message'=>'storebundle.flash.tag.edited')
            ));

            return $this->redirectToRoute('tag_edit', array('tag_id' => $tag->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array('%name%'=>$tag->getName()),'storebundle.prefix.tags');

        return $this->render('LilWorksStoreBundle:Tag:edit.html.twig', array(
            'tag' => $tag,
            'form' => $editForm->createView()
        ));
    }
    /**
     * @ParamConverter("tag", options={"mapping": {"tag_id"   : "id"}})
     */
    public function populateAction(Request $request, Tag $tag)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.populate %name%',array('%name%'=>$tag->getName()),'storebundle.prefix.tag');
        $object = array(
            'id'=>$tag->getId(),
            'entity'=>'LilWorksStoreBundle:Tag',
            'child'=>'products',
            'childEntity'=>'LilWorksStoreBundle:Product',
            'childMethod'=>'tag',
        );
        return $this->render('LilWorksStoreBundle:Tag:populate.html.twig', array(
            'tag' => $tag,
            'object'=>$object
        ));
    }

    /**
     * @ParamConverter("tag", options={"mapping": {"tag_id"   : "id"}})
     */
    public function emptyAction(Request $request,Tag $tag)
    {

        $em = $this->getDoctrine()->getManager();

        foreach($tag->getProducts() as $product){
            $tag->removeProduct($product);
            $product->removeTag($tag);
            $em->persist($product);
        }
        $em->persist($tag);
        $em->flush();

        $this->get('store.flash')->setMessages(array(
            array('status'=>'success','message'=>'storebundle.flash.tag.empty')
        ));

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('tag_index');
        } else {
            return $this->redirect($referer);
        }


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

        $this->get('store.flash')->setMessages(array(
            array('status'=>'success','message'=>'storebundle.flash.tag.deleted')
        ));


        if ( !$referer || is_null($referer)  ) {
            return $this->redirectToRoute('tag_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
