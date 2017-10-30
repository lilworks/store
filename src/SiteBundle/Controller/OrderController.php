<?php

namespace SiteBundle\Controller;


use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\OrdersOrderSteps;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;


class OrderController extends Controller
{



    public function indexAction(Request $request){


        $user = $this->getUser();

        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('o')
            ->from('LilWorksStoreBundle:Order','o')
            ->where('o.customer = :user')
            ->setParameter('user',$user->getCustomer()->getId())
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $translator=$this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('sitebundle.htmltitle.order'));


        return $this->render('SiteBundle:Order:index.html.twig',array(
            'user'=>$user,
            'pagination'=>$pagination
        ));
    }
    /**
     * @ParamConverter("get", class="LilWorksStoreBundle:Order", options={"id" = "id"})
     */
    public function pdfAction(Request $request,Order $order)
    {

        $em    = $this->getDoctrine()->getEntityManager();

        $param = array();
        $param["ORDER_LOGO_TOP"] = $em->getRepository('LilWorksStoreBundle:StoreInfo')->findOneByTag("ORDER_LOGO_TOP");
        $param["ORDER_TEXT_TOP"] =$em->getRepository('LilWorksStoreBundle:StoreInfo')->findOneByTag("ORDER_TEXT_TOP");

        $html = $this->renderView('SiteBundle:Order:pdf.html.twig', array(
            'order'  => $order,
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
            'param'=>$param
        ));

        return $this->render('SiteBundle:Order:pdf.html.twig',array(
            'order'  => $order,
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
            'param'=>$param
        ));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array(
                //'orientation' => 'landscape',
                //'enable-javascript' => true,
                //'javascript-delay' => 1000,
                'no-stop-slow-scripts' => true,
                'no-background' => false,
                'lowquality' => false,
                'encoding' => 'utf-8',
                'images' => true,
                'cookie' => array(),
                'dpi' => 300,
                'image-dpi' => 300,
                'enable-external-links' => true,
                'enable-internal-links' => true
            )),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="order_'.$order->getId().'.pdf"'
            )
        );
    }
    /**
     * @ParamConverter("get", class="LilWorksStoreBundle:Order", options={"id" = "id"})
     */
    public function editAction(Request $request,Order $order){

        $translator=$this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('sitebundle.htmltitle.order %reference%') , array('%reference%'=>$order->getReference() ));


        return $this->render('SiteBundle:Order:edit.html.twig',array(
            'order'=>$order
        ));
    }
    /**
     * @ParamConverter("get", class="LilWorksStoreBundle:Order", options={"id" = "id"})
     */
    public function payAction(Request $request,Order $order){

        $paymentService = $this->get('site.payment');
        $paymentMethodsForm = array();


        $paymentMethods = $this->getDoctrine()->getRepository("LilWorksStoreBundle:PaymentMethod")->findBy(array('isPublished'=>1));
        foreach($paymentMethods as $paymentMethod){
            $paymentService->setPaymentMethod($paymentMethod,$order);
            array_push($paymentMethodsForm,$paymentService->getForm());
        }

        $translator=$this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('sitebundle.htmltitle.order.pay %reference%') , array('%reference%'=>$order->getReference() ));


        return $this->render('SiteBundle:Order:pay.html.twig',array(
            'order'=>$order,
            'paymentMethods'=>$paymentMethods,
            'paymentMethodsForm'=>$paymentMethodsForm
        ));
    }

    public function payedAction(Request $request,Order $order){

        var_dump($order->getId());
        $em = $this->getDoctrine()->getEntityManager();
        $orderStep =  $em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PAYED");

        $orderUtils = $this->get('lilworks.store.order.utils');
        if(!$order->getReference())
            $order->setReference($orderUtils->setOrder($order)->getNextReference());
        $orderOrderStep = new OrdersOrderSteps();
        $orderOrderStep->setOrderStep($orderStep);
        $orderOrderStep->setOrder($order);

        $orderUtils->setOrder($order)->manageStock();


        $em->persist($orderOrderStep);
        $em->persist($order);

        var_dump($orderUtils->setOrder($order)->getNextReference());

        $em->flush();
        die();


        return $this->render('SiteBundle:Order:payed.html.twig',array(
            'order'=>$order,
        ));
    }


    /**
     * @ParamConverter("get", class="LilWorksStoreBundle:Order", options={"id" = "id"})
     */
    public function removeAction(Request $request,Order $order){

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($order);
        $em->flush();
        return $this->redirectToRoute('site_order');
    }

}
