<?php

namespace LilWorks\StoreBundle\Controller;


use LilWorks\StoreBundle\Entity\Conversation;
use LilWorks\StoreBundle\Filter\ConversationFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Conversation controller.
 *
 */
class ConversationController extends Controller
{
    /**
     * Lists all cupon entities.
     *
     */
    public function indexAction(Request $request)
    {

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');
        $formFilter = $this->get('form.factory')->create(ConversationFilterType::class);


        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();


        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Conversation')
                ->createQueryBuilder('c')
                ->join('c.messages','m')

            ;

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $qb);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Conversation')
                ->createQueryBuilder('c')
                ->join('c.messages','m')
                ;
        }

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            array('wrap-queries'=>true)
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.conversation.index'));

        return $this->render('LilWorksStoreBundle:Conversation:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter' => $formFilter->createView(),
        ));
    }

   

    /**
     * Creates a new conversation entity.
     *
     */
    public function newAction(Request $request)
    {
        $conversation = new Conversation();
        $em = $this->getDoctrine()->getManager();


        $form = $this->createForm('LilWorks\StoreBundle\Form\ConversationType', $conversation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conversation->setReference($em->getRepository('LilWorksStoreBundle:Conversation')->getNextReference($conversation));
            $em->persist($conversation);
            $em->flush();

            return $this->redirectToRoute('conversation_show', array('id' => $conversation->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.conversation.new'));


        return $this->render('LilWorksStoreBundle:Conversation:new.html.twig', array(
            'conversation' => $conversation,
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("conversation", options={"mapping": {"conversation_id"   : "id"}})
     */
    public function showAction(Conversation $conversation)
    {

        $em = $this->getDoctrine()->getManager();

        foreach($conversation->getMessages() as $message){
            if($message->getIsResponse() != 1){
                $message->setReadedAt(new \DateTime());
                $em->persist($message);
            }
        }
        $em->flush();

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.conversation.show %subject%',array("%subject%"=>$conversation->getConversationSubject())));

        return $this->render('LilWorksStoreBundle:Conversation:show.html.twig', array(
            'conversation' => $conversation,

        ));
    }

    /**
     * @ParamConverter("conversation", options={"mapping": {"conversation_id"   : "id"}})
     */
    public function editAction(Request $request, Conversation $conversation)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\ConversationType', $conversation);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {


            $em->persist($conversation);
            $em->flush();

            return $this->redirectToRoute('conversation_edit', array('id' => $conversation->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.conversation.edit %reference%',array("%reference%"=>$conversation->getReference())));


        return $this->render('LilWorksStoreBundle:Conversation:edit.html.twig', array(
            'conversation' => $conversation,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("conversation", options={"mapping": {"conversation_id"   : "id"}})
     */
    public function deleteAction(Request $request,Conversation $conversation)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($conversation);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('conversation_index');
        } else {
            return $this->redirect($referer);
        }

    }

}
