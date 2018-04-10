<?php

namespace LilWorks\StoreBundle\Controller;


use LilWorks\StoreBundle\Entity\SuperCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LilWorks\StoreBundle\Filter\SuperCategoryFilterType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * SuperCategory controller.
 *
 */
class SuperCategoryController extends Controller
{
    /**
     * Lists all superCategory entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(SuperCategoryFilterType::class);


        $rowsLiveEditor      = $this->get('app.rowsEditor')->setActions('LilWorksStoreBundle:SuperCategory',array(
            "delete"=> [ array('categories','==',0) ],
            "empty"=>[ array('categories', 'categories','>',0) ],
            "cols"=>array(
                "isPublished"=> array('boolean', "cond"=>array('categories','>',0)),
            )
        ));

        if ($request->isXMLHttpRequest()) {
            return new Response($rowsLiveEditor->doTheJob());
        }

        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:SuperCategory')
                ->createQueryBuilder('b');
            /*
                        $filterBuilder
                            ->leftJoin('LilWorksStoreBundle:Product','pr','WITH','pr.brand = b.id')
                            ->groupBy("b.id");
            */
            // build the query from the given form object
            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:SuperCategory')
                ->createQueryBuilder('b')
                // ->leftJoin('LilWorksStoreBundle:Product','pr','WITH','pr.brand = b.id')
                // ->groupBy("b.id")
            ;
        }

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.supercategories');

        return $this->render('LilWorksStoreBundle:SuperCategory:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter'=>$formFilter->createView(),
            'rowsLiveEditor'=>$rowsLiveEditor
        ));
    }

    /**
     * Creates a new superCategory entity.
     *
     */
    public function newAction(Request $request)
    {
        $superCategory = new SuperCategory();
        $form = $this->createForm('LilWorks\StoreBundle\Form\SuperCategoryType', $superCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();




            foreach($superCategory->getSupercategoriesCategories() as $superCategoryCategory){
                $superCategoryCategory->setCategory($superCategoryCategory->getCategory());
                $superCategoryCategory->setSuperCategory($superCategory);

                $em->persist($superCategoryCategory);
                $superCategory->addSupercategoriesCategory($superCategoryCategory);
            }

            $em->persist($superCategory);
            $em->flush();

            return $this->redirectToRoute('supercategory_show', array('supercategory_id' => $superCategory->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.supercategories');

        return $this->render('LilWorksStoreBundle:SuperCategory:new.html.twig', array(
            'superCategory' => $superCategory,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("superCategory", options={"mapping": {"supercategory_id"   : "id"}})
     */
    public function showAction(Request $request,SuperCategory $superCategory = null)
    {
        if(!$superCategory)
            return $this->redirectToRoute('customer_index');

        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array('%name%'=>$superCategory->getName()),'storebundle.prefix.supercategories');

        return $this->render('LilWorksStoreBundle:SuperCategory:show.html.twig', array(
            'superCategory' => $superCategory
        ));
    }

    /**
     * @ParamConverter("superCategory", options={"mapping": {"supercategory_id"   : "id"}})
     */
    public function editAction(Request $request, SuperCategory $superCategory = null)
    {

        if(!$superCategory)
            return $this->redirectToRoute('customer_index');

        $em = $this->getDoctrine()->getManager();


        $originalSuperCategoriesCategories = new ArrayCollection();
        // Create an ArrayCollection of the current shippingmethodCountry objects in the database
        foreach ($superCategory->getSupercategoriesCategories() as $superCategoryCategory) {
            $originalSuperCategoriesCategories->add($superCategoryCategory);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\SuperCategoryType', $superCategory);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {


            // remove the relationship between the tag and the Task
            foreach ($originalSuperCategoriesCategories as $superCategoryCategory) {


                if (false === $superCategory->getSupercategoriesCategories()->contains($superCategoryCategory)) {
                    $superCategoryCategory->getSuperCategory()->removeSupercategoriesCategory($superCategoryCategory);
                    $em->persist($superCategoryCategory);
                    $em->remove($superCategoryCategory);
                }


            }

            foreach ($superCategory->getSupercategoriesCategories() as $superCategoryCategoryFromForm) {
                $superCategoryCategoryFromForm->setSuperCategory($superCategory);
                $em->persist($superCategoryCategoryFromForm);

            }

            $em->persist($superCategory);
            $em->flush();

            return $this->redirectToRoute('supercategory_edit', array('supercategory_id' => $superCategory->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array('%name%'=>$superCategory->getName()),'storebundle.prefix.supercategories');

        return $this->render('LilWorksStoreBundle:SuperCategory:edit.html.twig', array(
            'superCategory' => $superCategory,
            'form' => $editForm->createView()
        ));
    }


    /**
     * @ParamConverter("superCategory", options={"mapping": {"supercategory_id"   : "id"}})
     */
    public function emptyAction(Request $request,SuperCategory $superCategory)
    {
        $em = $this->getDoctrine()->getManager();

        foreach($superCategory->getSupercategoriesCategories() as $superCategoryCategory){

            $em->remove($superCategoryCategory);
        }

        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('supercategory_index');
        } else {
            return $this->redirect($referer);
        }


    }


    /**
     * @ParamConverter("superCategory", options={"mapping": {"supercategory_id"   : "id"}})
     */
    public function deleteAction(Request $request,SuperCategory $superCategory)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($superCategory);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('supercategory_index');
        } else {
            return $this->redirect($referer);
        }


    }

}
