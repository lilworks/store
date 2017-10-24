<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Text;
use LilWorks\StoreBundle\Entity\TextBackup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\MonologBundle\DependencyInjection\MonologExtension;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\TextFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Text controller.
 *
 */
class TextController extends Controller
{
    /**
     * Lists all brand entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(TextFilterType::class);

        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Text')
                ->createQueryBuilder('t');



            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Text')
                ->createQueryBuilder('b')
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
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.text.index'));

        return $this->render('LilWorksStoreBundle:Text:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView()
        ));
    }

    /**
     * @ParamConverter("textBackup", options={"mapping": {"textbackup_id"   : "id"}})
     */
    public function backupShowAction(Request $request,TextBackup $textBackup){
        return $this->render('LilWorksStoreBundle:Text:show-backup.html.twig', array(
            'backup' => $textBackup,
            'text' => $textBackup->getOriginalText(),
        ));
    }
    /**
     * @ParamConverter("textBackup", options={"mapping": {"textbackup_id"   : "id"}})
     */
    public function backupDeleteAction(Request $request,TextBackup $textBackup){
        $em = $this->getDoctrine()->getManager();
        //$textBackup->getOriginalText()->removeBackup($textBackup);
        $em->remove($textBackup);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('text_index');
        } else {
            return $this->redirect($referer);
        }
    }
    /**
     * @ParamConverter("textBackup", options={"mapping": {"textbackup_id"   : "id"}})
     */
    public function backupApplyAction(Request $request,TextBackup $textBackup){
        $em = $this->getDoctrine()->getManager();

        $text = $textBackup->getOriginalText();

        $newBackup = new TextBackup();
        $newBackup->setTitle($text->getTitle());
        $newBackup->setContent($text->getContent());
        $newBackup->setCss($text->getCss());
        $newBackup->setOriginalText($text);

        $em->persist($newBackup);

        $text->setTitle($textBackup->getTitle());
        $text->setContent($textBackup->getContent());
        $text->setCss($textBackup->getCss());



        $em->persist($text);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('text_index');
        } else {
            return $this->redirect($referer);
        }
    }
    /**
     * @ParamConverter("text", options={"mapping": {"text_id"   : "id"}})
     */
    public function backupEmptyAction(Request $request,Text $text){

        $em = $this->getDoctrine()->getManager();

        foreach($text->getBackups() as $backup){
            //$backup->getOriginalText()->removeBackup($backup);
            $em->remove($backup);
        }


        $em->flush();


        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('text_index');
        } else {
            return $this->redirect($referer);
        }
    }
    /**
     * @ParamConverter("text", options={"mapping": {"text_id"   : "id"}})
     */
    public function backupAction( Request $request,Text $text)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQueryBuilder()
            ->select('b , MD5(b.css) as md5_css,MD5(b.content) as md5_content,MD5(b.title) as md5_title')
            ->from('LilWorksStoreBundle:TextBackup','b')
            ->where('b.originalText = :textId')
            ->setParameter('textId',$text->getId())
        ;

        if($text->getTitle()){
            $q->andHaving('md5_title = :title')->setParameter('title',  md5($text->getTitle()) );
        }
        if($text->getCss()){
            $q->andHaving('md5_title = :css')->setParameter('css',  md5($text->getCss()) );
        }
        if($text->getContent()){
            $q->andHaving('md5_content = :content')->setParameter('content',  md5($text->getContent()) );
        }

        $existingBackup = $q->getQuery()->getArrayResult();
/*
        $logger = $this->get('logger');
            $logger->info( $q->getQuery()->getDQL());
            $logger->addWarning('ExistingBackups:' , $existingBackup)
        ;
*/
        if(count($existingBackup) == 0){
            $backup = new TextBackup();
            $backup->setOriginalText($text);
            $backup->setCss($text->getCss());
            $backup->setContent($text->getContent());
            $backup->setTitle($text->getTitle());
            $text->addBackup($backup);
            $em->persist($backup);
            $em->flush();
        }



        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('text_index');
        } else {
            return $this->redirect($referer);
        }


    }
    /**
     * Creates a new text entity.
     *
     */
    public function newAction(Request $request)
    {
        $text = new Text();
        $form = $this->createForm('LilWorks\StoreBundle\Form\TextType', $text);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $text->setIsContent(1);
            $em->persist($text);
            $em->flush();

            return $this->redirectToRoute('text_show', array('text_id' => $text->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.text.new'));

        return $this->render('LilWorksStoreBundle:Text:new.html.twig', array(
            'text' => $text,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("text", options={"mapping": {"text_id"   : "id"}})
     */
    public function showAction(Text $text)
    {

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.text.show %name%',array('%name%'=>$text->getName())));

        return $this->render('LilWorksStoreBundle:Text:show.html.twig', array(
            'text' => $text
        ));
    }

    /**
     * @ParamConverter("text", options={"mapping": {"text_id"   : "id"}})
     */
    public function editAction(Request $request, Text $text)
    {

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\TextType', $text);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($text);
            $em->flush();
            return $this->redirectToRoute('text_edit', array('text_id' => $text->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.text.edit %name%',array('%name%'=>$text->getName())));



        return $this->render('LilWorksStoreBundle:Text:edit.html.twig', array(
            'text' => $text,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("text", options={"mapping": {"text_id"   : "id"}})
     */
    public function deleteAction(Request $request,Text $text)
    {
        $em = $this->getDoctrine()->getManager();

        if($text->getIsContent() == 1){
            $em->remove($text);
            $em->flush();
        }
        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('text_index');
        } else {
            return $this->redirect($referer);
        }

    }

}
