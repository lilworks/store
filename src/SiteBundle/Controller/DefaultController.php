<?php

namespace SiteBundle\Controller;

use LilWorks\StoreBundle\Entity\Conversation;
use LilWorks\StoreBundle\Entity\ConversationMessage;
use LilWorks\StoreBundle\Entity\Product;
use LilWorks\StoreBundle\Entity\Category;
use LilWorks\StoreBundle\Entity\SuperCategory;
use LilWorks\StoreBundle\Entity\Brand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SiteBundle\Filter\ProductInCategoryFilterType;
use SiteBundle\Filter\ProductInSuperCategoryFilterType;
use SiteBundle\Filter\ProductInAllFilterType;
use SiteBundle\Filter\ProductInBrandFilterType;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $basket = $this->get('site.basket');
        $carrousel = $this->get('site.carrousel')->getCarrousel();

        $this->get('site.setSeo')->setTitle('sitebundle.home');

        return $this->render('SiteBundle:Default:index.html.twig',array(
            'carrousel'=>$carrousel,
            'basket'=>$basket,
        ));
    }
    public function contactAction(Request $request)
    {
        $translator = $this->get('translator');
        $sent = null;
        $basket = $this->get('site.basket');
        $conversationMessage = new ConversationMessage();

        $form = $this->createForm('SiteBundle\Form\ConversationMessageType',$conversationMessage);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if(!$conversationMessage->getConversation()){
                $conversation = new Conversation();
                $conversation->setUser($this->getUser());
                $conversation->setConversationSubject($conversationMessage->getMessageSubject());
                $conversation->addMessage($conversationMessage);
                $conversation->setEmail($form['email']->getData());
                $conversationMessage->setConversation($conversation);
               $em->persist($conversation);
            }
            $em->flush();

            if( $conversationMessage->getGetCopy() == 1 || $conversation->getSendmail() == 1){
                $message = (new \Swift_Message(
                    $translator->trans('site.conversation.emailsubject') . ' • ' .$conversationMessage->getMessageSubject()
                ))
                    ->setTo($conversation->getEmail())
                    ->setBody(
                        $this->renderView(
                            'SiteBundle:Emails:conversation.html.twig',
                            array('conversation' => $conversation)
                        ),
                        'text/html'
                    );
                $this->get('swiftmailer.mailer')->send($message);
            }
        }

        $this->get('site.setSeo')->setTitle('sitebundle.contact');

        return $this->render('SiteBundle:Default:contact.html.twig',array(
            'basket'=>$basket,
            'form' => $form->createView(),
            'content'=>$this->get('site.content')->getText('contact'),
            'conversationMessage'=>$conversationMessage
        ));
    }


    public function productAction(Product $product){
        $basket = $this->get('site.basket');
        $productService = $this->get('site.product');


/*
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($product->getBrand()->getName() . " " . $product->getName() . " - " . $seoPage->getTitle() )
            ->addMeta('name', 'description',  preg_replace('!\s+!', ' ',substr(strip_tags($product->getDescription()),0,100)))
            ->addMeta('property', 'og:title', $product->getBrand()->getName() . " " . $product->getName())
            ->addMeta('property', 'og:description', preg_replace('!\s+!', ' ',substr(strip_tags($product->getDescription()),0,100)))
        ;
*/
        $this->get('site.setSeo')->setTitle('sitebundle.product %name%',array('%name%'=>$product->getBrand()->getName() . " " . $product->getName()));
        return $this->render('SiteBundle:Default:product.html.twig',array(
            'product'=>$product,
            'basket'=>$basket,
            'productService'=>$productService
        ));
    }

    public function categoryAction(Request $request,Category $category){

        $productFilter = $request->get('product_filter');


        $basket = $this->get('site.basket');
        $productService = $this->get('site.product');

        $formFilter = $this->get('form.factory')->create(
            ProductInCategoryFilterType::class,
            array(
                'category'=>$category,
            )
        );

        $qb = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Product')
            ->createQueryBuilder('p');
        $qb
            ->select('p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('c.supercategories_categories', 'scc')
            ->leftJoin('scc.supercategory', 'sc')
            ->leftJoin('p.brand', 'b')
            ->where('c.id = ' . $category->getId())
            ->andWhere('p.isPublished = 1 ')
            ->andWhere('p.priceOnline is not null ')
            ->andWhere('b.isPublished = 1 ')

        ;


        if ($request->query->has($formFilter->getName())) {
            $formFilter->submit($request->query->get($formFilter->getName()));
            $qbUpdater = $this->get('lexik_form_filter.query_builder_updater');
            $qb = $qbUpdater->addFilterConditions($formFilter, $qb);
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            (isset($productFilter['results'])) ? $productFilter['results'] : 12
        );


        $this->get('site.setSeo')->setTitle('sitebundle.category %name%',array('%name%'=>$category->getName()));

        $brands = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Brand')
            ->createQueryBuilder('b')
            ->join('b.products','p')
            ->join('p.categories','c')
            ->join('c.supercategories_categories','scc')
            ->join('scc.supercategory','sc')
            ->where('c.id = :category_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            #->andWhere('COUNT(p)>0')
            ->orderBy('b.name','asc')
            ->setParameter('category_id',$category->getId())
            ->getQuery()
            ->getResult()
        ;
        $supercategories = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:SuperCategory')
            ->createQueryBuilder('sc')
            ->join('sc.supercategories_categories','scc')
            ->join('scc.category','c')
            ->join('c.products','p')
            ->join('p.brand','b')
            ->where('c.id = :category_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            #->andWhere('COUNT(p)>0')
            ->orderBy('sc.name','asc')
            ->setParameter('category_id',$category->getId())
            ->getQuery()
            ->getResult()
        ;

        return $this->render('SiteBundle:Default:category.html.twig',array(
            'supercategories'=>$supercategories,
            'brands'=>$brands,
            'category'=>$category,
            'basket'=>$basket,
            'productService'=>$productService,
            'pagination'=>$pagination,
            'formFilter'=>$formFilter->createView(),
        ));
    }

    public function brandAction(Request $request,Brand $brand){
        $basket = $this->get('site.basket');
        $productService = $this->get('site.product');

        $formFilter = $this->get('form.factory')->create(
            ProductInBrandFilterType::class,
            array('brand'=>$brand)
        );


        $qb = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Product')
            ->createQueryBuilder('p');
        $qb
            ->select('p')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('c.supercategories_categories', 'scc')
            ->leftJoin('scc.supercategory', 'sc')
            ->where('b.id = ' . $brand->getId())
            ->andWhere('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')
            ->andWhere('b.isPublished = 1')
        ;


        if ($request->query->has($formFilter->getName())) {
            $formFilter->submit($request->query->get($formFilter->getName()));
            $qbUpdater = $this->get('lexik_form_filter.query_builder_updater');
            $qb = $qbUpdater->addFilterConditions($formFilter, $qb);
        }


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            9
        );

        $this->get('site.setSeo')->setTitle('sitebundle.brand %name%',array('%name%'=>$brand->getName() ));

        $categories = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Category')
            ->createQueryBuilder('c')
            ->join('c.products','p')
            ->join('p.brand','b')
            ->where('b.id = :brand_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')
            #->andWhere('COUNT(p)>0')
            ->orderBy('c.name','asc')
            ->setParameter('brand_id',$brand->getId())
            ->getQuery()
            ->getResult()
            ;

        return $this->render('SiteBundle:Default:brand.html.twig',array(
            'brand'=>$brand,
            'categories'=>$categories,
            'basket'=>$basket,
            'productService'=>$productService,
            'pagination'=>$pagination,
            'formFilter'=>$formFilter->createView(),
        ));
    }


    public function supercategoryAction(Request $request,SuperCategory $superCategory){
        $basket = $this->get('site.basket');
        $productService = $this->get('site.product');

        $formFilter = $this->get('form.factory')->create(
            ProductInSuperCategoryFilterType::class,
            array(
                'supercategory'=>$superCategory,
            )
        );


        $qb = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Product')
            ->createQueryBuilder('p');
        $qb
            ->select('p')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('c.supercategories_categories', 'scc')
            ->leftJoin('scc.supercategory', 'sc')
            ->where('sc.id = ' . $superCategory->getId())
            ->andWhere('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')
            ->andWhere('b.isPublished = 1')

        ;


        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            $qbUpdater = $this->get('lexik_form_filter.query_builder_updater');
            $qb = $qbUpdater->addFilterConditions($formFilter, $qb);
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            9
        );

        $this->get('site.setSeo')->setTitle('sitebundle.brand %name%',array('%name%'=>$superCategory->getName() ));


        $brands = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Brand')
            ->createQueryBuilder('b')
            ->join('b.products','p')
            ->join('p.categories','c')
            ->join('c.supercategories_categories','scc')
            ->join('scc.supercategory','sc')
            ->where('sc.id = :supercategory_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            #->andWhere('COUNT(p)>0')
            ->orderBy('b.name','asc')
            ->setParameter('supercategory_id',$superCategory->getId())
            ->getQuery()
            ->getResult()
        ;

        $categories = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Category')
            ->createQueryBuilder('c')
            ->join('c.products','p')
            ->join('p.brand','b')
            ->join('c.supercategories_categories','scc')
            ->join('scc.supercategory','sc')
            ->where('sc.id = :supercategory_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')

            #->andWhere('COUNT(p)>0')
            ->orderBy('c.name','asc')
            ->setParameter('supercategory_id',$superCategory->getId())
            ->getQuery()
            ->getResult()
        ;
        return $this->render('SiteBundle:Default:supercategory.html.twig',array(
            'supercategory'=>$superCategory,
            'brands'=>$brands,
            'categories'=>$categories,
            'basket'=>$basket,
            'productService'=>$productService,
            'pagination'=>$pagination,
            'formFilter'=>$formFilter->createView(),
        ));
    }




    public function allAction(Request $request){
       # $filterDatas = $request->get('product_filter');
       # $products = $this->getDoctrine()->getRepository('LilWorksStoreBundle:Product')->findForAll($filterDatas['search']);




        $basket = $this->get('site.basket');
        $productService = $this->get('site.product');

        $formFilter = $this->get('form.factory')->create(
            ProductInAllFilterType::class
        );


        $qb = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Product')
            ->createQueryBuilder('p');
        $qb
            ->select('p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('c.supercategories_categories', 'scc')
            ->leftJoin('scc.supercategory', 'sc')
            ->leftJoin('p.brand', 'b')
            ->andWhere('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')
            ->andWhere('b.isPublished = 1')
        ;


        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));
            $qbUpdater = $this->get('lexik_form_filter.query_builder_updater');
            $qb = $qbUpdater->addFilterConditions($formFilter, $qb);
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            9
        );


        $this->get('site.setSeo')->setTitle('sitebundle.htmltitle.allstore');

        $categories = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LilWorksStoreBundle:Category')
            ->createQueryBuilder('c')
            ->join('c.products','p')
            ->join('p.brand','b')
            ->where('p.isPublished = 1')
            ->andWhere('c.isPublished = 1')
            #->andWhere('COUNT(p)>0')
            ->orderBy('c.name','asc')
            ->getQuery()
            ->getResult()
        ;

        return $this->render('SiteBundle:Default:all.html.twig',array(
            'basket'=>$basket,
            'categories'=>$categories,
            'productService'=>$productService,
            'pagination'=>$pagination,
            'formFilter'=>$formFilter->createView(),
        ));
    }
}
