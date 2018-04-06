<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Filter\UserFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     */
    public function indexAction(Request $request)
    {


        $formFilter = $this->get('form.factory')->create(UserFilterType::class);
        if ($request->query->has($formFilter->getName())) {

            $formFilter->submit($request->query->get($formFilter->getName()));
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->createQueryBuilder('u')
                ->leftJoin('u.customer','c')
                ->groupBy('u.id')
            ;


            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->createQueryBuilder('u')
                ->leftJoin('u.customer','c')
                ->groupBy('u.id')
                ;
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            array('defaultSortFieldName' => 'u.lastLogin', 'defaultSortDirection' => 'desc')
        );

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('appbundle.htmltitle.user.index'));

        return $this->render('AppBundle:User:index.html.twig', array(
            'pagination' => $pagination,
            'formFilter'=>$formFilter->createView()
        ));
    }

    /**
     * Creates a new user entity.
     *
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', array('user_id' => $user->getId()));
        }
        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('appbundle.htmltitle.user.new'));

        return $this->render('AppBundle:User:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

/*
 * @ParamConverter("user", converter="app_get_or_redirect_to_list_converter" , options={"repository_method" = "find" , "mapping": {"user_id"   : "id"}})
 */
    /**
     * @ParamConverter("user" , options={ "mapping": {"user_id"   : "id"}})
     */
    public function showAction(User $user = null)
    {
        if(!$user)
            return $this->redirectToRoute('user_index');


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('appbundle.htmltitle.user.index %username%',array('%username%',$user->getEmail())));

        return $this->render('AppBundle:User:show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @ParamConverter("user", options={"mapping": {"user_id"   : "id"}})
     */
    public function editAction(Request $request, User $user = null)
    {
        if(!$user)
            return $this->redirectToRoute('user_index');


        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', array('user_id' => $user->getId()));
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('appbundle.htmltitle.user.index  %username%',array('%username%',$user->getEmail())));


        return $this->render('AppBundle:User:edit.html.twig', array(
            'user' => $user,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @ParamConverter("user", options={"mapping": {"user_id"   : "id"}})
     */
    public function deleteAction(Request $request,User $user)
    {

        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        $referer = $request->headers->get('referer');


        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('user_index');
        } else {
            return $this->redirect($referer);
        }

    }
}
