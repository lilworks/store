<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\CategoryFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{


    /**
     * Lists all Category entities.
     *
     */
    public function indexAction(Request $request)
    {
        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $rowsLiveEditor      = $this->get('app.rowsEditor')->setActions('LilWorksStoreBundle:Category',array(
            "delete"=> [ array('products','==',0) ],
            "empty"=>[ array('products', 'products','>',0) ],
            "cols"=>array(
                "isPublished"=> array('boolean', "cond"=>array('products','>',0)),
            )
        ));


        if ($request->isXMLHttpRequest()) {
            return new Response($rowsLiveEditor->doTheJob());
        }


        $formFilter = $this->get('form.factory')->create(CategoryFilterType::class);

        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Category')
                ->createQueryBuilder('c')
                ->leftJoin('c.products','p')
            ;

            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Category')
                ->createQueryBuilder('c')
                ->leftJoin('c.products','p')
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
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.category.index'));


        return $this->render('LilWorksStoreBundle:Category:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView(),
            'rowsLiveEditor'=>$rowsLiveEditor
        ));
    }

    /**
     * Creates a new Category entity.
     *
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm('LilWorks\StoreBundle\Form\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach($category->getProducts() as $product){
                $product->addCategory($category);
                $em->persist($product);
            }

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_show', array('id' => $category->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.category.new'));


        return $this->render('LilWorksStoreBundle:Category:new.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("category", options={"mapping": {"category_id"   : "id"}})
     */
    public function showAction(Category $category)
    {

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.category.show %name%',array('%name%'=>$category->getName())));


        return $this->render('LilWorksStoreBundle:Category:show.html.twig', array(
            'category' => $category
        ));
    }

    /**
     * @ParamConverter("category", options={"mapping": {"category_id"   : "id"}})
     */
    public function editAction(Request $request, Category $category)
    {
        $editForm = $this->createForm('LilWorks\StoreBundle\Form\CategoryType', $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach($category->getProducts() as $product){
                if (false === $product->getCategories()->contains($category)) {
                    $product->addCategory($category);
                    $em->persist($product);
                }

            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('category_edit', array('id' => $category->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('category edit'));

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.category.edit %name%',array('%name%'=>$category->getName())));



        return $this->render('LilWorksStoreBundle:Category:edit.html.twig', array(
            'category' => $category,
            'form' => $editForm->createView()
        ));
    }
    /**
     * @ParamConverter("category", options={"mapping": {"category_id"   : "id"}})
     */
    public function emptyAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        foreach($category->getProducts() as $product){
            $category->removeProduct($product);
            $product->removeCategory($category);

        }
        $em->persist($category);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('brand_index');
        } else {
            return $this->redirect($referer);
        }

    }
    /**
     * @ParamConverter("category", options={"mapping": {"category_id"   : "id"}})
     */
    public function deleteAction(Request $request,Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($category);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('category_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
