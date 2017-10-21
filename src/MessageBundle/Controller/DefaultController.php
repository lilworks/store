<?php

namespace MessageBundle\Controller;

use Lilworks\MessageBundle\Controller\DefaultController as BaseController;
use Lilworks\MessageBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends BaseController
{

    public function formAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $basket = $this->get("site.basket");
        $response = parent::formAction($request);
        $content = $response->getContent();

        $text = $em->getRepository('LilWorksStoreBundle:Text')->findOneByTag('contact');

        return $this->render('MessageBundle:Default:myform.html.twig',array(
            "content"=>$content,
            "basket"=>$basket,
            "text"=>$text->getText()
        ));

    }

    public function sentAction(Message $message)
    {
        $basket = $this->get("site.basket");
        $response = parent::sentAction($message);
        $content = $response->getContent();

        return $this->render('MessageBundle:Default:mysent.html.twig',array(
            "content"=>$content,
            "basket"=>$basket
        ));

    }
}
