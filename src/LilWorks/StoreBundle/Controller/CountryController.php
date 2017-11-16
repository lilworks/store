<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Country;
use LilWorks\StoreBundle\Entity\ShippingMethodsCountries;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * Country controller.
 *
 */
class CountryController extends Controller
{
    /**
     * Lists all country entities.
     *
     */
    public function indexAction(Request $request)
    {

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');

        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from('LilWorksStoreBundle:Country','p')
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );


        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.countries');

        return $this->render('LilWorksStoreBundle:Country:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor
        ));
    }

    /**
     * Creates a new country entity.
     *
     */
    public function newAction(Request $request)
    {
        $country = new Country();
        $form = $this->createForm('LilWorks\StoreBundle\Form\CountryType', $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($country);
            $em->flush();

            return $this->redirectToRoute('country_show', array('country_id' => $country->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.countries');

        return $this->render('LilWorksStoreBundle:Country:new.html.twig', array(
            'country' => $country,
            'form' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("country", options={"mapping": {"country_id"   : "id"}})
     */
    public function showAction(Country $country)
    {

        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array("%name%"=>$country->getName()),'storebundle.prefix.countries');

        return $this->render('LilWorksStoreBundle:Country:show.html.twig', array(
            'country' => $country,
        ));
    }

    /**
     * @ParamConverter("country", options={"mapping": {"country_id"   : "id"}})
     */
    public function editAction(Request $request, Country $country)
    {

        $em = $this->getDoctrine()->getManager();


        $originalShippingmethodsCountries = new ArrayCollection();
        // Create an ArrayCollection of the current shippingmethodCountry objects in the database
        foreach ($country->getShippingmethodsCountries() as $shippingmethodCountry) {
            $originalShippingmethodsCountries->add($shippingmethodCountry);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\CountryType', $country);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {


            // remove the relationship between the tag and the Task
            foreach ($originalShippingmethodsCountries as $shippingmethodCountry) {


                if (false === $country->getShippingmethodsCountries()->contains($shippingmethodCountry)) {
                    // remove the Task from the Tag
                    $shippingmethodCountry->getCountry()->removeShippingmethodsCountry($shippingmethodCountry);
                    // if it was a many-to-one relationship, remove the relationship like this
                    //$shippingmethodCountry->setCountry(null);
                    $em->persist($shippingmethodCountry);
                    // if you wanted to delete the Tag entirely, you can also do that
                     $em->remove($shippingmethodCountry);
                }


            }

            foreach ($country->getShippingmethodsCountries() as $shippingmethodCountryFromForm) {
                $shippingmethodCountryFromForm->setCountry($country);
                $em->persist($shippingmethodCountryFromForm);

            }

            $em->persist($country);
            $em->flush();

            return $this->redirectToRoute('country_edit', array('country_id' => $country->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array("%name%"=>$country->getName()),'storebundle.prefix.countries');

        return $this->render('LilWorksStoreBundle:Country:edit.html.twig', array(
            'country' => $country,
            'form' => $editForm->createView()
        ));
    }
    /**
     * @ParamConverter("shippingMethodsCountries", options={"mapping": {"shippingmethod_country_id"   : "id"}})
     */
    public function shippingMethodAction(Request $request, ShippingMethodsCountries $shippingMethodsCountries)
    {

        $em = $this->getDoctrine()->getManager();

        $originalTriggers = new ArrayCollection();
        // Create an ArrayCollection of the current shippingmethodCountry objects in the database
        foreach ($shippingMethodsCountries->getTriggers() as $t) {
            $originalTriggers->add($t);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\ShippingMethodsCountriesType', $shippingMethodsCountries);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {


            foreach ($originalTriggers as $t) {

                if (false === $shippingMethodsCountries->getTriggers()->contains($t)) {
                    $t->setShippingMethodCountry(null);
                    // if it was a many-to-one relationship, remove the relationship like this
                    //$shippingmethodCountry->setCountry(null);
                    //$em->persist($t);
                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($t);
                }


            }

            foreach ($shippingMethodsCountries->getTriggers() as $triggerFromForm) {
                $triggerFromForm->setShippingMethodCountry($shippingMethodsCountries);
                $em->persist($triggerFromForm);

            }

            $em->flush();
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array("%name%"=>$shippingMethodsCountries->getCountry()->getName()),'storebundle.prefix.countries');

        return $this->render('LilWorksStoreBundle:Country:shippingMethodCountry-edit.html.twig', array(
            'shippingMethodsCountries' => $shippingMethodsCountries,
            'form' => $editForm->createView()
        ));
    }


    /**
     * @ParamConverter("country", options={"mapping": {"country_id"   : "id"}})
     */
    public function deleteAction(Request $request,Country $country)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($country);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('country_index');
        } else {
            return $this->redirect($referer);
        }

    }
}
