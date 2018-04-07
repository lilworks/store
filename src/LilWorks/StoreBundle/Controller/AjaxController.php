<?php

namespace LilWorks\StoreBundle\Controller;


use LilWorks\StoreBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
/**
 * Ajax controller.
 *
 */
class AjaxController extends Controller
{
    public function ajaxCustomerAction(Request $request)
    {
        $form = $this->createForm('LilWorks\StoreBundle\Form\CustomerType', new Customer());
        $formDatas = $request->get('lilworks_storebundle_customer');

        $form->handleRequest($request);

        $customer = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {

            foreach($customer->getAddresses() as $address){
                $address->setCustomer($customer);
            }
            foreach($customer->getPhonenumbers() as $phonenumber){
                $phonenumber->setCustomer($customer);
            }

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($customer);
            $em->flush();

            return new Response(json_encode(array(
                'success'=>true,
                'data'=>array(
                    'id'=>$customer->getId(),
                    'firstName'=>$customer->getFirstName(),
                    'lastName'=>$customer->getLastName(),
                    'companyName'=>$customer->getCompanyName()
                )
            )));

        }


        return new Response(json_encode(array(
            'success'=>false,
            'data'=>array()
        )));




    }
    public function styleAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/css');

        $em = $this->getDoctrine()->getManager();
        $css = "";
        foreach($em->getRepository("LilWorksStoreBundle:Text")->findByExportInBase(1) as $text){
            $css.=$text->getCss();
        }
        //die($css);

        $response = new Response($css);
        $filename = 'text.css';
        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;

    }

    public function liveEditorAction(Request $request,$entity,$fieldName,$eid)
    {

        $getFormName = array_keys($request->request->all());
        $datas = $request->get("lilworks_ajax_".$entity."_".$fieldName);



        $liveEditor = $this->container->get("order.liveEditor");
        $liveEditor->getForm(
            $eid,
            $entity,
            $fieldName,
            $datas[$fieldName]

        );



        return new Response($datas[$fieldName]);

    }



}
