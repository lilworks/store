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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('sitebundle.htmltitle.conversations'));


        return $this->render('SiteBundle:Conversation:index.html.twig', array(
            'pagination'=>$pagination
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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('sitebundle.htmltitle.conversations.message.new'));


        return $this->render('SiteBundle:Conversation:new.html.twig', array(
            'conversation'=>$conversation,
            "form"=>$form->createView(),
            'sent'=>$sent
        ));
    }


}