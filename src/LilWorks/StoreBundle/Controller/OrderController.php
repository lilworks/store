<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Filter\OrderFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Order controller.
 *
 */
class OrderController extends Controller
{
    /**
     * Lists all order entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(OrderFilterType::class);



        if ($request->query->has($formFilter->getName())) {

            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Order')
                ->createQueryBuilder('o')
                ->join('o.customer','c')
            ;

            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Order')
                ->createQueryBuilder('o')
                ->join('o.customer','c')
            ;
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            array(
                'defaultSortFieldName' => 'o.createdAt',
                'defaultSortDirection' => 'desc',
            )
        );
        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.order.index'));

        return $this->render('LilWorksStoreBundle:Order:index.html.twig', array(
            'pagination' => $pagination,
            'formFilter'=>$formFilter->createView(),
        ));
    }
    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function devistofactureAction(Request $request, Order $order)
    {
        $em = $this->getDoctrine()->getManager();


        $orderType = $em->getRepository("LilWorksStoreBundle:OrderType")->findOneByTag("FACTURE");
        $newOrder = clone $order;

        $newOrder->setCreatedAt(null);
        $newOrder->setUpdatedAt(null);
        $newOrder->setOrderType($orderType);
        $newOrder->setReference($this->get('lilworks.store.order.utils')->setOrder($newOrder)->getNextReference());
        foreach($order->getOrdersProducts() as $orderProduct){
            $newOrderProduct = clone $orderProduct;
            $newOrderProduct->setOrder($newOrder);
            $newOrder->addOrdersProduct($newOrderProduct);
        }
        $em->persist($newOrder);
        $em->flush();
        return $this->redirectToRoute('order_edit', array('id' => $newOrder->getId()));
    }

    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function pdfAction(Request $request,Order $order)
    {

        $em = $this->getDoctrine()->getManager();
        $textHeader = $em->getRepository("LilWorksStoreBundle:Text")->findOneByTag('pdf-header');
        $textFooter = $em->getRepository("LilWorksStoreBundle:Text")->findOneByTag('pdf-footer');

        $header = $this->renderView('LilWorksStoreBundle:Pdf:header.html.twig', array(
            'css'=>$textHeader->getCss(),
            'text'=>$textHeader->getContent()
        ));

        $footer = $this->renderView('LilWorksStoreBundle:Pdf:footer.html.twig', array(
            'css'=>$textFooter->getCss(),
            'text'=>$textFooter->getContent()
        ));

        $html = $this->renderView('LilWorksStoreBundle:Order:pdf.html.twig', array(
            'order'  => $order,
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));
        $pdf = $this->get('knp_snappy.pdf');
        $pdf->setOption('footer-html', $footer);
        $pdf->setOption('footer-left', "[page]/[topage]");
        $pdf->setOption('header-html', $header);

        $filename = $order->getReference();

        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$filename.'"'
            )
        );
    }

    /**
     * Creates a new order entity.
     *
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();

        $order = new Order();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('LilWorks\StoreBundle\Form\OrderType', $order,array(
            'context'=>$this->container->getParameter('context'),
            'orderUtils' => $this->get('lilworks.store.order.utils')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($order->getOrdersPaymentMethods() as $orderPaymentMethodFromForm) {
                $orderPaymentMethodFromForm->setOrder($order);
                $em->persist($orderPaymentMethodFromForm);
            }
            foreach ($order->getOrdersProducts() as $orderProductFromForm) {
                $orderProductFromForm->setOrder($order);
                $em->persist($orderProductFromForm);
            }

            foreach ($order->getOrdersRealShippingMethods() as $orderRealShippingMethodFromForm) {
                $orderRealShippingMethodFromForm->setOrder($order);
                $em->persist($orderRealShippingMethodFromForm);
            }

            foreach ($order->getOrdersOrderSteps() as $orderOrderStepFromForm) {
                $orderOrderStepFromForm->setOrder($order);
                $em->persist($orderOrderStepFromForm);
            }

            $order->setTot($order->tot());
            $order->setPayed($order->payed());
            $order->setUserAsSeller($user);

            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('order_edit', array('order_id' => $order->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.order.new'));

        return $this->render('LilWorksStoreBundle:Order:new.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function showAction(Order $order)
    {


        if($order->getOrderType() && ($order->getOrderType()->getTag() == "FACTURE" || $order->getOrderType()->getTag() == "DEVIS"))
            $view = "show-".strtolower($order->getOrderType()->getTag());
        else
            $view = "show";

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.order.show %reference%',array('%reference%'=>$order->getReference())));

        return $this->render('LilWorksStoreBundle:Order:'.$view.'.html.twig', array(
            'order' => $order
        ));
    }

    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function editAction(Request $request, Order $order)
    {

        $user = $this->getUser();
        $orderUtils = $this->get('lilworks.store.order.utils') ;

        $em = $this->getDoctrine()->getManager();

        $originalOrderRealShippingMethods = new ArrayCollection();
        foreach ($order->getOrdersRealShippingMethods() as $orderRealShippingMethod) {
            $originalOrderRealShippingMethods->add($orderRealShippingMethod);
        }

        $originalOrdersPaymentMethods = new ArrayCollection();
        foreach ($order->getOrdersPaymentMethods() as $orderPaymentMethod) {
            $originalOrdersPaymentMethods->add($orderPaymentMethod);
        }

        $originalOrdersOrderSteps = new ArrayCollection();
        foreach ($order->getOrdersOrderSteps() as $originalOrderOrderStep) {
            $originalOrdersOrderSteps->add($originalOrderOrderStep);
        }

        $originalOrdersProducts = new ArrayCollection();
        foreach ($order->getOrdersProducts() as $originalOrderProduct) {
            $originalOrdersProducts->add($originalOrderProduct);
        }

        $form = $this->createForm('LilWorks\StoreBundle\Form\OrderType', $order,array(
            'context'=>$this->container->getParameter('context'),
            'orderUtils' => $orderUtils
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            foreach ($originalOrdersPaymentMethods as $orderPaymentMethod) {
                if (false === $order->getOrdersPaymentMethods()->contains($orderPaymentMethod)) {
                    $orderPaymentMethod->getOrder()->removeOrdersPaymentMethod($orderPaymentMethod);
                    $em->persist($orderPaymentMethod);
                    $em->remove($orderPaymentMethod);
                }
            }
            foreach ($order->getOrdersPaymentMethods() as $orderPaymentMethodFromForm) {
                $orderPaymentMethodFromForm->setOrder($order);
                $em->persist($orderPaymentMethodFromForm);
            }


            foreach ($originalOrdersProducts as $orderProduct) {
                if (false === $order->getOrdersProducts()->contains($orderProduct)) {
                    $orderProduct->getOrder()->removeOrdersProduct($orderProduct);
                    $em->persist($orderProduct);
                    $em->remove($orderProduct);
                }
            }
            foreach ($order->getOrdersProducts() as $orderProductFromForm) {
                $orderProductFromForm->setOrder($order);
                $em->persist($orderProductFromForm);
            }

            foreach ($originalOrderRealShippingMethods as $orderRealShippingMethod) {
                if (false === $order->getOrdersRealShippingMethods()->contains($orderRealShippingMethod)) {
                    $orderRealShippingMethod->getOrder()->removeOrdersRealShippingMethod($orderRealShippingMethod);
                    foreach($orderRealShippingMethod->getOrder()->getOrdersProducts() as $orderProduct){
                        $orderProduct->setOrderRealShippingMethod(null);
                        $em->persist($orderProduct);
                    }
                    $em->persist($orderRealShippingMethod);
                    $em->remove($orderRealShippingMethod);
                }
            }

            foreach ($order->getOrdersRealShippingMethods() as $orderRealShippingMethodFromForm) {
                $orderRealShippingMethodFromForm->setOrder($order);
                $em->persist($orderRealShippingMethodFromForm);
            }



            foreach ($originalOrdersOrderSteps as $orderOrderStep) {
                if (false === $order->getOrdersOrderSteps()->contains($orderOrderStep)) {
                    $orderOrderStep->getOrder()->removeOrdersOrderStep($orderOrderStep);
                    $em->persist($orderOrderStep);
                    $em->remove($orderOrderStep);
                }
            }

            foreach ($order->getOrdersOrderSteps() as $orderOrderStepFromForm) {
                $orderOrderStepFromForm->setOrder($order);
                $em->persist($orderOrderStepFromForm);
            }
            $order->setUserAsSeller($user);
            $order->setTot($order->tot());
            $order->setPayed($order->payed());

            if(!$order->getReference()){
                $order->setReference(
                    $orderUtils->getNextReference($order)
                );
            }

            $em->persist($order);
            $orderUtils->setOrder($order)->manageStock();

            $em->flush();



            return $this->redirectToRoute('order_edit', array('order_id' => $order->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.order.edit %reference%',array('%reference%'=>$order->getReference())));


        return $this->render('LilWorksStoreBundle:Order:edit.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function deleteAction(Request $request,Order $order)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($order);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('order_index');
        } else {
            return $this->redirect($referer);
        }


    }


}
