<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Docfile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\DocfileFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Docfile controller.
 *
 */
class DocfileController extends Controller
{
    /**
     * Lists all Docfile entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(DocfileFilterType::class);

        if ($request->query->has($formFilter->getName())) {
            $formFilter->submit($request->query->get($formFilter->getName()));
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Docfile')
                ->createQueryBuilder('d');
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Docfile')
                ->createQueryBuilder('d')
               ;
        }

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.docfiles');

        return $this->render('LilWorksStoreBundle:Docfile:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView()
        ));
    }

    /**
     * Creates a new docfile entity.
     *
     */
    public function newAction(Request $request)
    {
        $docfile = new Docfile();
        $form = $this->createForm('LilWorks\StoreBundle\Form\DocfileType', $docfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach( $docfile->getProducts() as $p){
                if(!$p->getDocfiles()->contains($docfile)){
                    $p->addDocfile($docfile);
                    $docfile->addProduct($p);
                    $em->persist($p);
                }
            }
            $em->flush();
            return $this->redirectToRoute('docfile_show', array('docfile_id' => $docfile->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.docfiles');

        return $this->render('LilWorksStoreBundle:Docfile:new.html.twig', array(
            'docfile' => $docfile,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("docfile", options={"mapping": {"docfile_id"   : "id"}})
     */
    public function showAction(Docfile $docfile)
    {
        $this->get('store.setSeo')->setTitle('storebundle.title.show %title%',array('%title%'=>$docfile->getTitle()),'storebundle.prefix.docfiles');

        return $this->render('LilWorksStoreBundle:Docfile:show.html.twig', array(
            'docfile' => $docfile
        ));
    }

    /**
     * @ParamConverter("docfile", options={"mapping": {"docfile_id"   : "id"}})
     */
    public function editAction(Request $request, Docfile $docfile)
    {


        $editForm = $this->createForm('LilWorks\StoreBundle\Form\DocfileType', $docfile);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('docfile_edit', array('docfile_id' => $docfile->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %title%',array('%title%'=>$docfile->getTitle()),'storebundle.prefix.docfiles');

        return $this->render('LilWorksStoreBundle:Docfile:edit.html.twig', array(
            'docfile' => $docfile,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("docfile", options={"mapping": {"docfile_id"   : "id"}})
     */
    public function deleteAction(Request $request,Docfile $docfile)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($docfile);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('docfile_index');
        } else {
            return $this->redirect($referer);
        }


    }

    /**
     * @ParamConverter("docfile", options={"mapping": {"docfile_id"   : "id"}})
     */
    public function emptyAction(Request $request,Docfile $docfile)
    {

        $em = $this->getDoctrine()->getManager();

        foreach($docfile->getProducts() as $product){
            $docfile->removeProduct($product);
            $product->removeDocfile($docfile);
            $em->persist($product);
        }
        $em->persist($docfile);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('docfile_index');
        } else {
            return $this->redirect($referer);
        }


    }

    /**
     * @ParamConverter("docfile", options={"mapping": {"docfile_id"   : "id"}})
     */
    public function downloadAction(Request $request,Docfile $docfile)
    {

        $response = new BinaryFileResponse($this->getParameter('kernel.root_dir')."/../web/docfile/product/". $docfile->getDocName());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;


    }



}
