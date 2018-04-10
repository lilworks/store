<?php

namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Customer;
use LilWorks\StoreBundle\Filter\CustomerFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Customer controller.
 *
 */
class CustomerController extends Controller
{
    /**
     * Lists all customer entities.
     *
     */
    public function indexAction(Request $request)
    {

        $simpleLiveEditor    = $this->get('app.simpleLiveEditor');
        $formFilter = $this->get('form.factory')->create(CustomerFilterType::class);

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

        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();


        if ($request->query->has($formFilter->getName())) {
            // manually bind values from the request
            $formFilter->submit($request->query->get($formFilter->getName()));

            // initialize a query builder
            $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('LilWorksStoreBundle:Customer')
                ->createQueryBuilder('c')
            ;

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($formFilter, $qb);

        }else{
            $qb->select('c')->from('LilWorksStoreBundle:Customer','c');
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            array('defaultSortFieldName' => 'c.createdAt', 'defaultSortDirection' => 'desc')
        );

        $this->get('store.setSeo')->setTitle('storebundle.title.list',array(),'storebundle.prefix.customers');


        return $this->render('LilWorksStoreBundle:Customer:index.html.twig', array(
            'pagination' => $pagination,
            'simple_live_editor'=>$simpleLiveEditor,
            'formFilter' => $formFilter->createView(),
        ));
    }

    /**
     * Creates a new customer entity.
     *
     */
    public function newAction(Request $request)
    {
        $customer = new Customer();
        $em = $this->getDoctrine()->getManager();


        $form = $this->createForm('LilWorks\StoreBundle\Form\CustomerType', $customer);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($customer->getPhonenumbers() as $phonenumberFromForm) {
                $phonenumberFromForm->setCustomer($customer);
                $em->persist($phonenumberFromForm);
            }

            foreach ($customer->getAddresses() as $addressFromForm) {
                $addressFromForm->setCustomer($customer);
                $em->persist($addressFromForm);
            }

            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('customer_show', array('customer_id' => $customer->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.new',array(),'storebundle.prefix.customers');

        return $this->render('LilWorksStoreBundle:Customer:new.html.twig', array(
            'customer' => $customer,
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("customer", options={"mapping": {"customer_id"   : "id"}})
     */
    public function showAction(Customer $customer = null)
    {

        if(!$customer)
            return $this->redirectToRoute('customer_index');

        $this->get('store.setSeo')->setTitle('storebundle.title.show %name%',array(
            "%name%"=>$customer->getLastName() . " " .  $customer->getFirstName()  . " " .  $customer->getCompanyName()
        ),'storebundle.prefix.customers');

        return $this->render('LilWorksStoreBundle:Customer:show.html.twig', array(
            'customer' => $customer,

        ));
    }

    /**
     * @ParamConverter("customer", options={"mapping": {"customer_id"   : "id"}})
     */
    public function editAction(Request $request, Customer $customer)
    {
        $em = $this->getDoctrine()->getManager();


        $originalPhonenumbers = new ArrayCollection();
        // Create an ArrayCollection of the current shippingmethodCountry objects in the database
        foreach ($customer->getPhonenumbers() as $phonenumber) {
            $originalPhonenumbers->add($phonenumber);
        }
        $originalAddresses = new ArrayCollection();

        foreach ($customer->getAddresses() as $address) {
            $originalAddresses->add($address);
        }

        $editForm = $this->createForm('LilWorks\StoreBundle\Form\CustomerType', $customer);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            foreach ($originalPhonenumbers as $phonenumber) {
                if (false === $customer->getPhonenumbers()->contains($phonenumber)) {
                    // remove the Task from the Tag
                    $phonenumber->getCustomer()->removePhonenumber($phonenumber);
                    // if it was a many-to-one relationship, remove the relationship like this
                    //$shippingmethodCountry->setCountry(null);
                    $em->persist($phonenumber);
                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($phonenumber);
                }
            }
            foreach ($customer->getPhonenumbers() as $phonenumberFromForm) {
                $phonenumberFromForm->setCustomer($customer);
                $em->persist($phonenumberFromForm);
            }

            foreach ($originalAddresses as $address) {
                if (false === $customer->getAddresses()->contains($address)) {
                    $address->getCustomer()->removeAddress($address);
                    $em->persist($address);
                    $em->remove($address);
                }
            }
            foreach ($customer->getAddresses() as $addressFromForm) {
                $addressFromForm->setCustomer($customer);
                $em->persist($addressFromForm);
            }

            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('customer_edit', array('id' => $customer->getId()));
        }

        $this->get('store.setSeo')->setTitle('storebundle.title.edit %name%',array(
            "%name%"=>$customer->getLastName() . " " .  $customer->getFirstName()  . " " .  $customer->getCompanyName()
        ),'storebundle.prefix.customers');


        return $this->render('LilWorksStoreBundle:Customer:edit.html.twig', array(
            'customer' => $customer,
            'form' => $editForm->createView()
        ));
    }

    /**
     * @ParamConverter("customer", options={"mapping": {"customer_id"   : "id"}})
     */
    public function deleteAction(Request $request,Customer $customer)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($customer);
        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('customer_index');
        } else {
            return $this->redirect($referer);
        }

    }

}
