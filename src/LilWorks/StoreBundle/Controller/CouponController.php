<?php

namespace LilWorks\StoreBundle\Controller;


use LilWorks\StoreBundle\Entity\Coupon;
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

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.coupon.index'));

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

        if($coupon->getCustomer() && !$coupon->getAddress()){
            return $this->redirectToRoute('customer_edit', array('_fragment' => 'addresses','id' => $coupon->getCustomer()->getId()));
        }
        $filename = $coupon->getReference();



        $html = $this->renderView('LilWorksStoreBundle:Coupon:pdf.html.twig', array(
            'coupon'  => $coupon,
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));
        $header = $this->renderView('LilWorksStoreBundle:DepositSale:header.html.twig', array(
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));

        $footer = $this->renderView('LilWorksStoreBundle:DepositSale:footer.html.twig', array(
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));

        $pdf = $this->get('knp_snappy.pdf');
        $pdf->setOption('footer-html', $footer);
        $pdf->setOption('footer-left', "[page]/[topage]");
        $pdf->setOption('header-html', $header);

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
     * Creates a new coupon entity.
     *
     */
    public function newAction(Request $request)
    {
        $coupon = new Coupon();
        $em = $this->getDoctrine()->getManager();


        $form = $this->createForm('LilWorks\StoreBundle\Form\CouponType', $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coupon->setReference($em->getRepository('LilWorksStoreBundle:Coupon')->getNextReference($coupon));
            $em->persist($coupon);
            $em->flush();

            return $this->redirectToRoute('coupon_show', array('id' => $coupon->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.coupon.new'));


        return $this->render('LilWorksStoreBundle:Coupon:new.html.twig', array(
            'coupon' => $coupon,
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("coupon", options={"mapping": {"coupon_id"   : "id"}})
     */
    public function showAction(Coupon $coupon)
    {

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.coupon.show %reference%',array("%reference%"=>$coupon->getReference())));

        return $this->render('LilWorksStoreBundle:Coupon:show.html.twig', array(
            'coupon' => $coupon,

        ));
    }

    /**
     * @ParamConverter("coupon", options={"mapping": {"coupon_id"   : "id"}})
     */
    public function editAction(Request $request, Coupon $coupon)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\CouponType', $coupon);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {


            $em->persist($coupon);
            $em->flush();

            return $this->redirectToRoute('coupon_edit', array('id' => $coupon->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.coupon.edit %reference%',array("%reference%"=>$coupon->getReference())));


        return $this->render('LilWorksStoreBundle:Coupon:edit.html.twig', array(
            'coupon' => $coupon,
            'form' => $editForm->createView()
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
