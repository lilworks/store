<?php

namespace LilWorks\StoreBundle\Controller;


use LilWorks\StoreBundle\Entity\Coupon;
use LilWorks\StoreBundle\Entity\Customer;
use LilWorks\StoreBundle\Filter\CouponFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * coupon controller.
 *
 */
class CouponController extends Controller
{
    /**
     * Lists all cupon entities.
     *
     */
    public function indexAction(Request $request)
    {

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');
        $formFilter = $this->get('form.factory')->create(CouponFilterType::class);


        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();


        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Coupon')
                ->createQueryBuilder('c')
            ;

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $qb);

        }else{
            $qb->select('c')->from('LilWorksStoreBundle:Coupon','c');
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.coupons');

        return $this->render('LilWorksStoreBundle:Coupon:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter' => $formFilter->createView(),
        ));
    }

    /**
     * @ParamConverter("coupon", options={"mapping": {"coupon_id"   : "id"}})
     */
    public function pdfAction(Request $request, Coupon $coupon)
    {

        if(! $coupon->getCustomer() ){
            return $this->redirectToRoute('customer_edit', array('_fragment' => 'addresses','customer_id' => $coupon->getCustomer()->getId()));
        }

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
            'coupon'  => $coupon,

            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ),'LilWorksStoreBundle:Coupon:pdf.html.twig');



        if($coupon->getReference() != ""){
            $filename = $coupon->getReference() . ".pdf";
        }else{
            $filename = "coupon_".date('Y-m-d_H-i').".pdf";
        }


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
     * Creates a new coupon entity.
     *
     */
    public function newAction(Request $request)
    {
        $formCustomer = $this->createForm('LilWorks\StoreBundle\Form\CustomerType', new Customer());

        $coupon = new Coupon();
        $em = $this->getDoctrine()->getManager();


        $form = $this->createForm('LilWorks\StoreBundle\Form\CouponType', $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coupon->setReference($em->getRepository('LilWorksStoreBundle:Coupon')->getNextReference($coupon));
            $em->persist($coupon);
            $em->flush();

            return $this->redirectToRoute('coupon_show', array('coupon_id' => $coupon->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.coupons');


        return $this->render('LilWorksStoreBundle:Coupon:new.html.twig', array(
            'coupon' => $coupon,
            'form' => $form->createView(),
            'formCustomer'=>$formCustomer->createView()
        ));
    }

    /**
     * @ParamConverter("coupon", options={"mapping": {"coupon_id"   : "id"}})
     */
    public function showAction(Coupon $coupon)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array("%name%"=>$coupon->getReference()),'storebundle.prefix.coupons');

        return $this->render('LilWorksStoreBundle:Coupon:show.html.twig', array(
            'coupon' => $coupon,

        ));
    }

    /**
     * @ParamConverter("coupon", options={"mapping": {"coupon_id"   : "id"}})
     */
    public function editAction(Request $request, Coupon $coupon)
    {
        $formCustomer = $this->createForm('LilWorks\StoreBundle\Form\CustomerType', new Customer());

        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\CouponType', $coupon);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {


            $em->persist($coupon);
            $em->flush();

            return $this->redirectToRoute('coupon_edit', array('coupon_id' => $coupon->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array("%name%"=>$coupon->getReference()),'storebundle.prefix.coupons');

        return $this->render('LilWorksStoreBundle:Coupon:edit.html.twig', array(
            'coupon' => $coupon,
            'form' => $editForm->createView(),
            'formCustomer'=>$formCustomer->createView()
        ));
    }

    /**
     * @ParamConverter("coupon", options={"mapping": {"coupon_id"   : "id"}})
     */
    public function deleteAction(Request $request,Coupon $coupon)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($coupon);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('coupon_index');
        } else {
            return $this->redirect($referer);
        }

    }

}
