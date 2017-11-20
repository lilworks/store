<?php
namespace SiteBundle\Controller;

use LilWorks\StoreBundle\Entity\Conversation;
use LilWorks\StoreBundle\Entity\ConversationMessage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ConversationController extends Controller
{

    public function defaultAction(Request $request){

        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $qb = $this->get('doctrine.orm.entity_manager')
            ->getRepository('LilWorksStoreBundle:Conversation')
            ->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user',$user->getId())
        ;
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            array('defaultSortFieldName' => 'c.createdAt', 'defaultSortDirection' => 'desc')
        );


        $this->get('site.setSeo')->setTitle('sitebundle.conversations.home');

        return $this->render('SiteBundle:Conversation:index.html.twig', array(
            'pagination'=>$pagination
        ));
    }

    /**
     * @ParamConverter("conversation", options={"mapping": {"conversation_id"   : "id"}})
     */
    public function showAction(Request $request,Conversation $conversation){


        return $this->render('SiteBundle:Conversation:show.html.twig', array(
            'conversation'=>$conversation
        ));
    }

    public function newAction(Request $request){

        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $em = $this->getDoctrine()->getManager();

        $conversation = new Conversation();
        $conversationMessage = new ConversationMessage();

        $form = $this->createForm('SiteBundle\Form\ConversationMessageInCustomerType', $conversationMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conversationMessage->setConversation($conversation);
            $conversation->addMessage($conversationMessage);
            $conversation->setConversationSubject($conversationMessage->getMessageSubject());
            $conversation->setUser($user);
            $conversation->setEmail($user->getEmail());

            $em->persist($conversation);
            $em->persist($conversationMessage);
            $em->flush();
        }

        $this->get('site.setSeo')->setTitle('sitebundle.htmltitle.conversation.new');

        return $this->render('SiteBundle:Conversation:new.html.twig', array(
            "form"=>$form->createView(),
        ));
    }


    /**
     * @ParamConverter("conversation", options={"mapping": {"conversation_id"   : "id"}})
     */
    public function newMessageAction(Request $request,Conversation $conversation){

        $em = $this->getDoctrine()->getManager();
        $sent = false;
        $message = new ConversationMessage();
        $form = $this->createForm('SiteBundle\Form\MessageInConversationType', $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conversation->addMessage($message);
            $message->setConversation($conversation);

            $em->persist($message);
            $em->flush();

            $sent = true;
        }


        $this->get('site.setSeo')->setTitle('sitebundle.htmltitle.conversations.message.new');


        return $this->render('SiteBundle:Conversation:new-message.html.twig', array(
            'conversation'=>$conversation,
            "form"=>$form->createView(),
            'sent'=>$sent
        ));
    }

    /**
     * @ParamConverter("conversation", options={"mapping": {"conversation_id"   : "id"}})
     */
    public function removeAction(Request $request,Conversation $conversation){
        $user = $this->getUser();
        if (!is_object($user)  || $conversation->getUser() != $user) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

            $em = $this->getDoctrine()->getEntityManager();

        $em->remove($conversation);
        $em->flush();

        $this->get('site.flash')->setMessages(array(
               array('status'=>'error','message'=>'sitebundle.flash.conversation.removed')
        ));


        return $this->redirectToRoute('site_conversations');
    }
}