<?php

namespace LilWorks\StoreBundle\Controller;


use LilWorks\StoreBundle\Entity\Customer;
use LilWorks\StoreBundle\Form\CreateOrderFlow;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\AnnonceFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class FlowController extends Controller
{

    public function indexAction(Request $request)
    {
        $formData = new Customer(); // Your form data class. Has to be an object, won't work properly with an array.

        $flow = $this->get('lilworks.form.flow.order'); // must match the flow's service id
        $flow->bind($formData);

        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {
                // flow finished
                $em = $this->getDoctrine()->getManager();
                $em->persist($formData);
                $em->flush();

                $flow->reset(); // remove step data from the session

                return $this->redirect($this->generateUrl('home')); // redirect when done
            }
        }

        return $this->render('LilWorksStoreBundle:Flow:createOrder.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));


    }

}
