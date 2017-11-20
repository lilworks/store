<?php
namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
class CustomerController extends Controller
{

    public function DefaultAction(Request $request){
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $this->get('site.setSeo')->setTitle('sitebundle.customer.home');



        return $this->render('SiteBundle:Customer:index.html.twig', array(
            'user'=>$user
        ));
    }

    public function EditAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $form = $this->createForm('SiteBundle\Form\CustomerType', $user->getCustomer());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('site_customer');
        }


        return $this->render('SiteBundle:Customer:edit.html.twig', array(
            'form'=>$form->createView()
        ));
    }

    public function PhonenumbersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $user = $this->getUser();


        if(is_null($user)){
            return $this->redirectToRoute('fos_user_security_login');
        }
        $customer = $user->getCustomer();
        $form = $this->createForm('SiteBundle\Form\PhonenumbersType', $customer);
        $form->handleRequest($request);


        $currentPhonenumbers = $em->getRepository('LilWorksStoreBundle:Phonenumber')->findByCustomer($customer);
        $originalPhonenumbers = new ArrayCollection();
        // Create an ArrayCollection of the current shippingmethodCountry objects in the database
        foreach ($currentPhonenumbers as $phonenumber) {
            $originalPhonenumbers->add($phonenumber);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($originalPhonenumbers as $phonenumber) {
                if (false === $user->getCustomer()->getPhonenumbers()->contains($phonenumber)) {
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

            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('fos_user_profile_show');
        }



        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('stitebundle.htmltitle.customer.phonenumbers'));


        return $this->render('SiteBundle:Customer:phonenumbers.html.twig', array(
            'form' => $form->createView(),
            'customer' => $user->getCustomer(),
            'phonenumbers' => $user->getCustomer()->getPhonenumbers(),
            'errors'=>$form->getErrors()
        ));
    }



    public function AddressesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $user = $this->getUser();


        if(is_null($user)){
            return $this->redirectToRoute('fos_user_security_login');
        }
        $customer = $user->getCustomer();
        $form = $this->createForm('SiteBundle\Form\AddressesType', $customer);
        $form->handleRequest($request);


        $currentAddresses = $em->getRepository('LilWorksStoreBundle:Address')->findByCustomer($customer);
        $originalAddresses = new ArrayCollection();
        foreach ($currentAddresses as $address) {
            $originalAddresses->add($address);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($originalAddresses as $address) {
                if (false === $user->getCustomer()->getAddresses()->contains($address)) {
                    // remove the Task from the Tag
                    $address->getCustomer()->removeAddress($address);
                    // if it was a many-to-one relationship, remove the relationship like this
                    //$shippingmethodCountry->setCountry(null);
                    $em->persist($address);
                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($address);
                }
            }
            foreach ($customer->getAddresses() as $addressFromForm) {
                $addressFromForm->setCustomer($customer);
                $em->persist($addressFromForm);
            }

            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('fos_user_profile_show');
        }


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('stitebundle.htmltitle.customer.addresses'));

        return $this->render('SiteBundle:Customer:addresses.html.twig', array(
            'form' => $form->createView(),
            'customer' => $user->getCustomer(),
            'phonenumbers' => $user->getCustomer()->getAddresses(),
            'errors'=>$form->getErrors()
        ));
    }

}