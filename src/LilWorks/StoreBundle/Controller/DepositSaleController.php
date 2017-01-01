<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\DepositSale;
use LilWorks\StoreBundle\Filter\DepositSaleFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Collections\ArrayCollection;
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


        $em = $this->getDoctrine()->getManager();
        $textHeader = $em->getRepository("LilWorksStoreBundle:Text")->findOneByTag('pdf-header');
        $textFooter = $em->getRepository("LilWorksStoreBundle:Text")->findOneByTag('pdf-footer');
        $textCGV = $em->getRepository("LilWorksStoreBundle:Text")->findOneByTag('cgv_depot-vente');

        $header = $this->renderView('LilWorksStoreBundle:Pdf:header.html.twig', array(
            'css'=>$textHeader->getCss(),
            'text'=>$textHeader->getContent()
        ));

        $footer = $this->renderView('LilWorksStoreBundle:Pdf:footer.html.twig', array(
            'css'=>$textFooter->getCss(),
            'text'=>$textFooter->getContent()
        ));

        $html = $this->renderView('LilWorksStoreBundle:DepositSale:pdf-'.strtolower($depositSale->getStatus()->getTag()).'.html.twig', array(
            'depositSale'  => $depositSale,
            'CGV'=>$textCGV,
            'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
        ));
        $pdf = $this->get('knp_snappy.pdf');
        $pdf->setOption('footer-html', $footer);
        $pdf->setOption('footer-left', "[page]/[topage]");
        $pdf->setOption('header-html', $header);

        $filename = $depositSale->getReference(). ".pdf";

        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$filename.'"'
            )
        );


        if($depositSale->getCustomer() && !$depositSale->getAddress()){
            return $this->redirectToRoute('customer_edit', array('_fragment' => 'addresses','customer_id' => $depositSale->getCustomer()->getId()));
        }


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

                return $this->redirectToRoute('depositsale_edit', array('depositsale_id' => $depositSale->getId()));
            }else{
                return $this->redirectToRoute('customer_edit', array('customer_id' => $depositSale->getCustomer()->getId()));
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

        $originalDepositSalesPaymentMethods = new ArrayCollection();
        foreach ($depositSale->getDepositSalesPaymentMethods() as $depositSalePaymentMethod) {
            $originalDepositSalesPaymentMethods->add($depositSalePaymentMethod);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\DepositSaleType', $depositSale);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($originalDepositSalesPaymentMethods as $depositSalePaymentMethod) {
                if (false === $depositSale->getDepositSalesPaymentMethods()->contains($depositSalePaymentMethod)) {
                    $depositSalePaymentMethod->getDepositSale()->removeDepositSalesPaymentMethod($depositSalePaymentMethod);
                    $em->persist($depositSalePaymentMethod);
                    $em->remove($depositSalePaymentMethod);

                }
            }
            foreach ($depositSale->getDepositSalesPaymentMethods() as $deposilSalePaymentMethodFromForm) {
                $deposilSalePaymentMethodFromForm->setDepositSale($depositSale);
                $em->persist($deposilSalePaymentMethodFromForm);
            }


            $em->persist($depositSale);
            $em->flush();

            return $this->redirectToRoute('depositsale_edit', array('depositsale_id' => $depositSale->getId()));
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
            return $this->redirectToRoute('depositsale_index');
        } else {
            return $this->redirect($referer);
        }

    }

}
