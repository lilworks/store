<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Filter\SessionFilterType;

/**
 * Session controller.
 *
 */
class SessionController extends Controller
{
    /**
     * Lists all session entities.
     *
     */
    public function indexAction(Request $request)
    {
        $formFilter = $this->get('form.factory')->create(SessionFilterType::class);
        if ($request->query->has($formFilter->getName())) {

            $formFilter->submit($request->query->get($formFilter->getName()));
            $filterBuilder = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Session')
                ->createQueryBuilder('s')
                ->join('s.basket','b')
            ;


            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Session')
                ->createQueryBuilder('s')
                ->join('s.basket','b')

            ;
        }
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('AppBundle:Session:index.html.twig', array(
            'pagination' => $pagination,
            'formFilter'=>$formFilter->createView()

        ));
    }

    /**
     * Finds and displays a session entity.
     *
     */
    public function showAction(Session $session)
    {

        return $this->render('AppBundle:Session:show.html.twig', array(
            'session' => $session,
        ));
    }
}
