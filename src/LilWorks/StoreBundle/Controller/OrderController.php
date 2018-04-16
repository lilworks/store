<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Customer;
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

    public function coolAction(Request $request)
    {
        $user = $this->getUser();

        $order = new Order();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('LilWorks\StoreBundle\Form\CoolOrderType', $order,array(
            'context'=>$this->container->getParameter('context'),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order =  $form->getData();
            var_dump($order-getId());
            die();
        }
        return $this->render('LilWorksStoreBundle:Order:cool.html.twig', array(
            'form' => $form->createView(),
            'order'=>$order
        ));

    }
        /**
     * Lists all order entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(OrderFilterType::class);



        if ($request->query->has($formFilter->getName())) {
            $formFilter->submit($request->query->get($formFilter->getName()));
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Order')
                ->createQueryBuilder('o')
                ->join('o.customer','c')
                ->join('o.ordersProducts','op')
                ->join('op.product','p')
            ;
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);
        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Order')
                ->createQueryBuilder('o')
                ->join('o.customer','c')
                ->join('o.ordersProducts','op')
                ->join('op.product','p')
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
        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.orders');

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

        $newOrder->setCreatedAt(new \DateTime());
        $newOrder->setReference(null);
        $newOrder->setUpdatedAt(null);
        $newOrder->setOrderType($orderType);
        #$newOrder->setReference($this->get('lilworks.store.order.utils')->setOrder($newOrder)->getNextReference());
        foreach($order->getOrdersProducts() as $orderProduct){
            $newOrderProduct = clone $orderProduct;
            $newOrderProduct->setOrder($newOrder);
            $newOrder->addOrdersProduct($newOrderProduct);
        }
        $newOrder=$this->get('lilworks.store.orderManager')->setMakeFlush(false)->init($newOrder);


        $em->persist($newOrder);
        $em->flush();

        $this->get('store.flash')->setMessages(array(
            array('status'=>'success','message'=>'storebundle.flash.order.devistofacture')
        ));

        return $this->redirectToRoute('order_edit', array('order_id' => $newOrder->getId()));
    }


    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function pdfAction(Request $request,Order $order)
    {


        $em = $this->getDoctrine()->getManager();
        $pdf = $this->get('lilworks_store.pdf');
        $textHeader = $em->getRepository("LilWorksStoreBundle:Text")->findOneByTag('pdf-header');
        $pdf->setHeader(array(
            'css'=>$textHeader->getCss(),
            'text'=>$textHeader->getContent(),
        ));
        $textFooter = $em->getRepository("LilWorksStoreBundle:Text")->findOneByTag('pdf-footer');
        $pdf->setFooter(array(
            'css'=>$textFooter->getCss(),
            'text'=>$textFooter->getContent(),
        ));

        $pdf->setContent(array(
            'order'  => $order,

            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ),'LilWorksStoreBundle:Order:pdf.html.twig');


        $filename = $order->getReference(). ".pdf";
        return new Response(
            $pdf->getResponse(),
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
        $formCustomer = $this->createForm('LilWorks\StoreBundle\Form\CustomerType', new Customer());


        $user = $this->getUser();

        $order = new Order();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('LilWorks\StoreBundle\Form\OrderType', $order,array(
            'context'=>$this->container->getParameter('context'),
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

            $order->setUserAsSeller($user);

/*
            $manualCustomer = array(
                'firstName'=>$form['manualFirstName']->getData(),
                'lastName'=>$form['manualLastName']->getData(),
                'companyName'=>$form['manualCompanyName']->getData()
            );
            $this->get('lilworks.store.orderManager')->setMakeFlush(false)->setOrder($order,$manualCustomer);*/
            //$order=$this->get('lilworks.store.orderManager')->setMakeFlush(false)->init($order);
            $order=$this->get('lilworks.store.newOrderManager')->manage($order);

            $em->persist($order);
            $em->flush();

            #$this->get('lilworks.store.stockManager')->byOrder($order);

            $this->get('store.flash')->setMessages(array(
                array('status'=>'success','message'=>'storebundle.flash.order.created')
            ));
            return $this->redirectToRoute('order_edit', array('order_id' => $order->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.orders');

        return $this->render('LilWorksStoreBundle:Order:new.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
            'formCustomer' => $formCustomer->createView(),
        ));
    }

    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function showAction(Order $order = null)
    {
        if(!$order)
            return $this->redirectToRoute('order_index');

        if($order->getOrderType() && ($order->getOrderType()->getTag() == "FACTURE" || $order->getOrderType()->getTag() == "DEVIS"))
            $view = "show-".strtolower($order->getOrderType()->getTag());
        else
            $view = "show";


        $this->get('store.setSeo')->setTitle('storebundle.title.show %reference%',array('%reference%'=>$order->getReference()),'storebundle.prefix.orders');
        return $this->render('LilWorksStoreBundle:Order:'.$view.'.html.twig', array(
            'order' => $order,
            'returnsAllowed'=>$this->get('lilworks.store.orderManager')->returnAllowed($order)
        ));
    }

    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function editAction(Request $request, Order $order = null)
    {
        if(!$order)
            return $this->redirectToRoute('order_index');

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $formCustomer = $this->createForm('LilWorks\StoreBundle\Form\CustomerType', new Customer());


        $form = $this->createForm('LilWorks\StoreBundle\Form\OrderType', $order,array(
            'context'=>$this->container->getParameter('context'),
            //'orderUtils' => $orderUtils
        ));

        $originalOrdersProducts = new ArrayCollection();
        foreach ($order->getOrdersProducts() as $originalOrderProduct) {
            $originalOrdersProducts->add($originalOrderProduct);
        }
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

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

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


            foreach ($originalOrdersProducts as $orderProduct) {
                if (false === $order->getOrdersProducts()->contains($orderProduct)) {
                    $orderProduct->getOrder()->removeOrdersProduct($orderProduct);
                    $em->persist($orderProduct);
                    $em->remove($orderProduct);
                    $this->get('lilworks.store.newStockManager')->restockByOrderProduct($orderProduct);
                }
            }

            foreach ($order->getOrdersProducts() as $orderProductFromForm) {
                $orderProductFromForm->setOrder($order);
                $em->persist($orderProductFromForm);
            }


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





            $order=$this->get('lilworks.store.newOrderManager')->manage($order);


            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('order_edit', array('order_id' => $order->getId()));
        }



        $this->get('store.setSeo')->setTitle('storebundle.title.edit %reference%',array('%reference%'=>$order->getReference()),'storebundle.prefix.orders');
        return $this->render('LilWorksStoreBundle:Order:edit.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
            'formCustomer' => $formCustomer->createView(),
        ));
    }

    /**
     * @ParamConverter("order", options={"mapping": {"order_id"   : "id"}})
     */
    public function deleteAction(Request $request,Order $order)
    {
        $em = $this->getDoctrine()->getManager();


        $this->get('lilworks.store.newOrderManager')->removeOrder($order);




        $em->remove($order);
        $em->flush();



        $this->get('store.flash')->setMessages(array(
            array('status'=>'success','message'=>'storebundle.flash.order.deleted')
        ));

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('order_index');
        } else {
            return $this->redirect($referer);
        }


    }


}
