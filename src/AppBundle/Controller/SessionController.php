<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Filter\SessionFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
                ->leftJoin('s.basket','b')
            ;

            $qb = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $filterBuilder);

        }else{
            $qb = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Session')
                ->createQueryBuilder('s')
                ->leftJoin('s.basket','b')

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

    public function cleanAction()
    {
        $date = new \DateTime();


        foreach( $this->getDoctrine()
            ->getManager()
            ->createQuery('SELECT s FROM AppBundle:Session s WHERE ( s.time + s.lifetime) < :now')
            ->setParameter('now',$date->getTimestamp() )
            ->getResult() as $session ){
           $this->getDoctrine()->getManager()->remove($session);

        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('session_index');
    }
    /**
     * @ParamConverter("session", options={"mapping": {"session_id"   : "id"}})
     */
    public function deleteAction(Request $request, Session $session)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($session);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('session_index');
        } else {
            return $this->redirect($referer);
        }

    }
}
