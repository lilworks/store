<?php

namespace LilWorks\StoreBundle\Controller;


use LilWorks\StoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\ProductFilterType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Product controller.
 *
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     */
    public function indexAction(Request $request)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');
        $formFilter = $this->get('form.factory')->create(ProductFilterType::class);

        $rowsLiveEditor      = $this->get('app.rowsEditor')->setActions('LilWorksStoreBundle:Product',array(
            "delete"=> [ array('orders','==',0) ],
            "empty"=>[  ],
            "cols"=>array(
                "isPublished"=> array('boolean'),
            )
        ));


        if ($request->isXMLHttpRequest()) {
            return new Response($rowsLiveEditor->doTheJob());
        }

        if ($request->query->has($formFilter->getName())) {

            $formFilter->submit($request->query->get($formFilter->getName()));

            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Product')
                ->createQueryBuilder('p');

            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Product')
                ->createQueryBuilder('p')
            ;
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $request->query->getInt('maxItemPerPage', 10)
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.product.index'));


        return $this->render('LilWorksStoreBundle:Product:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView(),
            'rowsLiveEditor'=>$rowsLiveEditor,
            'pagination'=>$pagination
        ));
    }

    /**
     * Creates a new product entity.
     *
     */
    public function newAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $product = new Product();
        $form = $this->createForm('LilWorks\StoreBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', array('product_id' => $product->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.product.new'));

        return $this->render('LilWorksStoreBundle:Product:new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("product", options={"mapping": {"product_id"   : "id"}})
     */
    public function showAction(Product $product)
    {


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.product.show %name%',array('%name%'=>$product->getBrand()->getName() . " " .$product->getName())));

        return $this->render('LilWorksStoreBundle:Product:show.html.twig', array(
            'product' => $product,
        ));
    }

    /**
     * @ParamConverter("product", options={"mapping": {"product_id"   : "id"}})
     */
    public function editAction(Request $request, Product $product)
    {
        $em = $this->getDoctrine()->getManager();


        $originalTags = new ArrayCollection();
        foreach ($product->getTags() as $tag) {
            $originalTags->add($tag);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            if(count($product->getDocfiles() )>0){
                foreach ($product->getDocfiles() as $docfile) {
                    $docfile->addProduct($product);
                    $em->persist($docfile);
                }
            }

            if(count($product->getTags() )>0){
                foreach ($product->getTags() as $tag) {
                    $tag->addProduct($product);
                    $em->persist($tag);
                }
            }

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_edit', array('product_id' => $product->getId()));
        }
        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.product.edit %name%',array('%name%'=>$product->getBrand()->getName() . " " .$product->getName())));


        return $this->render('LilWorksStoreBundle:Product:edit.html.twig', array(
            'product' => $product,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("product", options={"mapping": {"product_id"   : "id"}})
     */
    public function deleteAction(Request $request,Product $product)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($product);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('product_index');
        } else {
            return $this->redirect($referer);
        }
    }

}
