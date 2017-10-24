<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Docfile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\DocfileFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Docfile')
                ->createQueryBuilder('d');


            // build the query from the given form object
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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.docfile.index'));

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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.docfile.new'));

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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.docfile.show %docname%',array(' %docname%'=>$docfile->getDocName())));

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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.docfile.edit %docname%',array(' %docname%'=>$docfile->getDocName())));


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

}
