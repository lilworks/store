<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Category;
use LilWorks\StoreBundle\Entity\OrderProductReturn;
use LilWorks\StoreBundle\Entity\OrdersProducts;
use LilWorks\StoreBundle\Entity\Coupon;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\CategoryFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use LilWorks\StoreBundle\Filter\OrderProductReturnFilterType;
/**
 * Category controller.
 *
 */
class OrderProductReturnController extends Controller
{
    /**
     * Lists all return entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(OrderProductReturnFilterType::class);



        if ($request->query->has($formFilter->getName())) {
            $formFilter->submit($request->query->get($formFilter->getName()));
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:OrderProductReturn')
                ->createQueryBuilder('opr')
                ->leftJoin('opr.orderProduct','op')
                ->leftJoin('op.order','o')
                ->leftJoin('o.customer','c')
            ;

            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);
        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:OrderProductReturn')
                ->createQueryBuilder('opr')
            ;
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.returns');

        return $this->render('LilWorksStoreBundle:OrderProductReturn:index.html.twig', array(
            'pagination' => $pagination,
            'formFilter'=>$formFilter->createView(),
        ));
    }

    /**
     * @ParamConverter("ordersProducts", options={"mapping": {"orderproduct_id"   : "id"}})
     */
    public function newAction(Request $request,OrdersProducts $ordersProducts)
    {
        $messages = array();
        $orderProductReturn = new OrderProductReturn();
        $orderProductReturn->setOrderProduct($ordersProducts);

        $form = $this->createForm('LilWorks\StoreBundle\Form\OrderProductReturnType', $orderProductReturn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();



            foreach ($orderProductReturn->getReturnsPaymentMethods() as $returnPaymentMethodFromForm) {

                $returnPaymentMethodFromForm->setOrderProductReturn($orderProductReturn);
                $em->persist($returnPaymentMethodFromForm);

                if($returnPaymentMethodFromForm->getPaymentMethod()->getTag()=="COUPON" && !$returnPaymentMethodFromForm->getCoupon()){
                    $coupon = new Coupon();
                    $coupon->setCustomer($ordersProducts->getOrder()->getCustomer());
                    $ordersProducts->getOrder()->getCustomer()->addCoupon($coupon);
                    $coupon->setAmount($returnPaymentMethodFromForm->getAmount());
                    $coupon->setReference(
                        $em->getRepository('LilWorksStoreBundle:Coupon')->getNextReference($coupon)
                    );
                    $coupon->setCreatedAt(new \DateTime());
                    $coupon->addReturnsPaymentMethod($returnPaymentMethodFromForm);
                    $returnPaymentMethodFromForm->setCoupon($coupon);
                    $em->persist($coupon);

                    array_push($messages,array(
                        'status'=>'success',
                        'message'=>'storebundle.flash.coupon.created %reference% %amount%',
                        'transParam'=>array('%reference%'=>$coupon->getReference(),'%amount%'=>$coupon->getAmount())
                    ));
                }

            }

            if(!$orderProductReturn->getReference() || $orderProductReturn->getReference() == ""){
                $orderProductReturn->setReference($em->getRepository('LilWorksStoreBundle:OrderProductReturn')->getNextReference($orderProductReturn));
            }


            /*
            $orderProduct = $orderProductReturn->getOrderProduct();
            $orderProduct->setDestocking($orderProduct->getQuantity() -  $orderProductReturn->getQuantity());
            $product = $orderProductReturn->getOrderProduct()->getProduct();
            $product->setStock(
                $orderProductReturn->getOrderProduct()->getProduct()->getStock() + $orderProductReturn->getQuantity()
            );

            $em->persist($orderProduct);
            $em->persist($product);
            */
            array_push($messages,array('status'=>'success','message'=>'storebundle.flash.return.restocking'));


            $em->persist($orderProductReturn);
            $em->flush();

            $this->get('lilworks.store.stockManager')->byReturn($orderProductReturn);

            array_push($messages,array('status'=>'success','message'=>'storebundle.flash.return.created'));

            $this->get('store.flash')->setMessages($messages);

            return $this->redirectToRoute('orderproductreturn_show', array('orderproductreturn_id' => $orderProductReturn->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.returns');

        return $this->render('LilWorksStoreBundle:OrderProductReturn:new.html.twig', array(
            'orderProductReturn' => $orderProductReturn,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("orderProductReturn", options={"mapping": {"orderproductreturn_id"   : "id"}})
     */
    public function editAction(Request $request,OrderProductReturn $orderProductReturn)
    {

        $messages = array();

        $form = $this->createForm('LilWorks\StoreBundle\Form\OrderProductReturnType', $orderProductReturn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();


            foreach ($orderProductReturn->getReturnsPaymentMethods() as $returnPaymentMethodFromForm) {

                $returnPaymentMethodFromForm->setOrderProductReturn($orderProductReturn);
                $em->persist($returnPaymentMethodFromForm);


                if($returnPaymentMethodFromForm->getPaymentMethod()->getTag()=="COUPON" && !$returnPaymentMethodFromForm->getCoupon()){
                    $ordersProducts = $orderProductReturn->getOrderProduct();
                    $coupon = new Coupon();
                    $coupon->setCustomer($ordersProducts->getOrder()->getCustomer());
                    $ordersProducts->getOrder()->getCustomer()->addCoupon($coupon);
                    $coupon->setAmount($returnPaymentMethodFromForm->getAmount());
                    $coupon->setReference(
                        $em->getRepository('LilWorksStoreBundle:Coupon')->getNextReference($coupon)
                    );
                    $coupon->setCreatedAt(new \DateTime());
                    $coupon->addReturnsPaymentMethod($returnPaymentMethodFromForm);
                    $returnPaymentMethodFromForm->setCoupon($coupon);
                    $em->persist($coupon);

                    array_push($messages,array(
                        'status'=>'success',
                        'message'=>'storebundle.flash.coupon.created %reference% %amount%',
                        'transParam'=>array('%reference%'=>$coupon->getReference(),'%amount%'=>$coupon->getAmount())
                    ));
                }

            }

            if(!$orderProductReturn->getReference() || $orderProductReturn->getReference() == ""){
                $orderProductReturn->setReference($em->getRepository('LilWorksStoreBundle:OrderProductReturn')->getNextReference($orderProductReturn));
            }


/*
            $orderProduct = $orderProductReturn->getOrderProduct();
            $orderProduct->setDestocking($orderProduct->getQuantity() -  $orderProductReturn->getQuantity());

            $product = $orderProductReturn->getOrderProduct()->getProduct();
            $product->setStock(
                $orderProductReturn->getOrderProduct()->getProduct()->getStock() + $orderProductReturn->getQuantity()
            );

            $em->persist($orderProduct);
            $em->persist($product);
*/
            $em->persist($orderProductReturn);
            $em->flush();
            $this->get('lilworks.store.stockManager')->byReturn($orderProductReturn);
            return $this->redirectToRoute('orderproductreturn_show', array('orderproductreturn_id' => $orderProductReturn->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.returns');

        return $this->render('LilWorksStoreBundle:OrderProductReturn:edit.html.twig', array(
            'orderProductReturn' => $orderProductReturn,
            'form' => $form->createView(),
        ));
    }
    /**
     * @ParamConverter("orderProductReturn", options={"mapping": {"orderproductreturn_id"   : "id"}})
     */
    public function showAction(Request $request,OrderProductReturn $orderProductReturn)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array('%name%'=>$orderProductReturn->getReference()),'storebundle.prefix.returns');

        return $this->render('LilWorksStoreBundle:OrderProductReturn:show.html.twig', array(
            'orderProductReturn' => $orderProductReturn,
        ));
    }
    /**
     * @ParamConverter("orderProductReturn", options={"mapping": {"orderproductreturn_id"   : "id"}})
     */
    public function deleteAction(Request $request,OrderProductReturn $orderProductReturn)
    {
        $em = $this->getDoctrine()->getManager();

        $messages = array();

        if(!$orderProductReturn->getIsArchived()){


            $em->remove($orderProductReturn);
            $em->flush();

            $this->get('lilworks.store.stockManager')->byOrder($orderProductReturn->getOrderProduct()->getOrder());

            array_push($messages,array('status'=>'success','message'=>'storebundle.flash.return.deleted'));

            $this->get('store.flash')->setMessages($messages);
        }else{
            $this->get('store.flash')->setMessages(array(
                array('status'=>'error','message'=>'storebundle.flash.return.notdeleted.isarchived')
            ));
        }

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('orderproductreturn_index');
        } else {
            return $this->redirect($referer);
        }


    }

    /**
     * @ParamConverter("orderProductReturn", options={"mapping": {"orderproductreturn_id"   : "id"}})
     */
    public function pdfAction(Request $request,OrderProductReturn $orderProductReturn)
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

        $html = $this->renderView('LilWorksStoreBundle:OrderProductReturn:pdf.html.twig', array(
            'orderProductReturn'  => $orderProductReturn,
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));
        $pdf = $this->get('knp_snappy.pdf');
        $pdf->setOption('footer-html', $footer);
        $pdf->setOption('footer-left', "[page]/[topage]");
        $pdf->setOption('header-html', $header);

        $filename = $orderProductReturn->getReference();

        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$filename.'"'
            )
        );
    }


}
