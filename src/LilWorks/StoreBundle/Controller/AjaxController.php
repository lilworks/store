<?php

namespace LilWorks\StoreBundle\Controller;


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
