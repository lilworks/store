<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\DepositSale;
use LilWorks\StoreBundle\Filter\DepositSaleFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * DepositSale controller.
 *
 */
class DepositSaleController extends Controller
{
    /**
     * Lists all DepositSale entities.
     *
     */
    public function indexAction(Request $request)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');
        $formFilter = $this->get('form.factory')->create(DepositSaleFilterType::class);

        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        if ($request->query->has($formFilter->getName())) {
            $formFilter->submit($request->query->get($formFilter->getName()));
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:DepositSale')
                ->createQueryBuilder('ds')
            ;
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $qb);
        }else{
            $qb->select('ds')->from('LilWorksStoreBundle:DepositSale','ds');
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            array('defaultSortFieldName' => 'ds.createdAt', 'defaultSortDirection' => 'desc')
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.depositsale.index'));

        return $this->render('LilWorksStoreBundle:DepositSale:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter' => $formFilter->createView(),
        ));
    }

    /**
     * @ParamConverter("depositSale", options={"mapping": {"depositsale_id"   : "id"}})
     */
    public function pdfAction(Request $request, DepositSale $depositSale)
    {

        if($depositSale->getCustomer() && !$depositSale->getAddress()){
            return $this->redirectToRoute('customer_edit', array('_fragment' => 'addresses','id' => $depositSale->getCustomer()->getId()));
        }
        $filename = $depositSale->getReference();

        $status = strtolower($depositSale->getStatus()->getTag());

        $html = $this->renderView('LilWorksStoreBundle:DepositSale:pdf-'.$status.'.html.twig', array(
            'depositSale'  => $depositSale,
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));
        $header = $this->renderView('LilWorksStoreBundle:DepositSale:header.html.twig', array(
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));

        $footer = $this->renderView('LilWorksStoreBundle:DepositSale:footer.html.twig', array(
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));
        /*
                $html = $this->renderView('LilWorksStoreBundle:DepositSale:pdf.html.twig', array(
                    'depositSale'  => $depositSale,
                    'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
                ));

                return $this->renderView('LilWorksStoreBundle:DepositSale:pdf-'.$status.'.html.twig', array(
                    'depositSale'  => $depositSale,
                    #'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
                ));
        */
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
     * Creates a new DepositSale entity.
     *
     */
    public function newAction(Request $request)
    {
        $depositSale = new DepositSale();
        $em = $this->getDoctrine()->getManager();


        $form = $this->createForm('LilWorks\StoreBundle\Form\DepositSaleType', $depositSale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $depositSale->setReference(
                $em->getRepository("LilWorksStoreBundle:DepositSale")->getNextReference($depositSale)
            );

            if(count($depositSale->getCustomer()->getAddresses())>0){


                foreach($depositSale->getCustomer()->getAddresses() as $address){
                    $depositSale->setAddress($address);
                    break;
                }

                $em->persist($depositSale);
                $em->flush();

                return $this->redirectToRoute('depositSale_edit', array('id' => $depositSale->getId()));
            }else{
                return $this->redirectToRoute('customer_edit', array('id' => $depositSale->getCustomer()->getId()));
            }
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.depositsale.new'));

        return $this->render('LilWorksStoreBundle:DepositSale:new.html.twig', array(
            'depositSale' => $depositSale,
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("depositSale", options={"mapping": {"depositsale_id"   : "id"}})
     */
    public function showAction(DepositSale $depositSale)
    {


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.depositsale.show %reference%',array('%reference%'=>$depositSale->getReference())));

        return $this->render('LilWorksStoreBundle:DepositSale:show.html.twig', array(
            'depositSale' => $depositSale,

        ));
    }

    /**
     * @ParamConverter("depositSale", options={"mapping": {"depositsale_id"   : "id"}})
     */
    public function editAction(Request $request, DepositSale $depositSale)
    {
        $em = $this->getDoctrine()->getManager();


        $editForm = $this->createForm('LilWorks\StoreBundle\Form\DepositSaleType', $depositSale);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {


            $em->persist($depositSale);
            $em->flush();

            return $this->redirectToRoute('depositSale_edit', array('id' => $depositSale->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.depositsale.edit %reference%',array('%reference%'=>$depositSale->getReference())));

        return $this->render('LilWorksStoreBundle:DepositSale:edit.html.twig', array(
            'depositSale' => $depositSale,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("depositSale", options={"mapping": {"depositsale_id"   : "id"}})
     */
    public function deleteAction(Request $request, DepositSale $depositSale)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($depositSale);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('depositSale_index');
        } else {
            return $this->redirect($referer);
        }

    }

}
