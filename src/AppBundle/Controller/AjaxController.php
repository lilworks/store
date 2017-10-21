<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajax controller.
 *
 */
class AjaxController extends Controller
{
    public function postAcceptorAction(Request $request){
        /*******************************************************
         * Only these origins will be allowed to upload images *
         ******************************************************/
        $accepted_origins = array("http://storeoffline", "http://localhost", "http://192.168.1.1", "http://example.com");

        /*********************************************
         * Change this line to set the upload folder *
         *********************************************/
        $imageFolder = $this->get('kernel')->getRootDir() . '/../web/ajaxupload/';

        reset ($_FILES);
        $temp = current($_FILES);



        if (is_uploaded_file($temp['tmp_name'])){
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // same-origin requests won't set an origin. If the origin is set, it must be valid.

                if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
                } else {
                    #header("HTTP/1.0 403 Origin Denied");
                    return new Response(
                        'Origin Denied',
                        Response::HTTP_FORBIDDEN,
                        array('content-type' => 'text/html')
                    );
                }
            }

            /*
              If your script needs to receive cookies, set images_upload_credentials : true in
              the configuration and enable the following two headers.
            */
            // header('Access-Control-Allow-Credentials: true');
            // header('P3P: CP="There is no P3P policy."');

            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                #header("HTTP/1.0 500 Invalid file name.");

                return new Response(
                    'Invalid file name.',
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    array('content-type' => 'text/html')
                );
            }

            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
                #header("HTTP/1.0 500 Invalid extension.");
                return new Response(
                    'Invalid extension.',
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    array('content-type' => 'text/html')
                );
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $imageFolder . $temp['name'];
            move_uploaded_file($temp['tmp_name'], $filetowrite);

            // Respond to the successful upload with JSON.
            // Use a location key to specify the path to the saved image resource.
            // { location : '/your/uploaded/image/file'}
            return new Response(
                json_encode(array('location' => "/ajaxupload/".$temp['name'])),
                Response::HTTP_OK,
                array('content-type' => 'text/html')
            );
        } else {
            // Notify editor that the upload failed
            #header("HTTP/1.0 500 Server Error");
            return new Response(
                'Server Error',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                array('content-type' => 'text/html')
            );
        }


    }
    public function searchEntityByFieldAction(Request $request){
        $entity = $request->get('entity');
        $field = $request->get('field');
        $searchValue = $request->get('searchValue');
        $exclude = $request->get('exclude');

        $results = $this->getDoctrine()->getRepository($entity)->createQueryBuilder('x')
            ->select('x.id , x.'.$field)
            ->where('x.'.$field.' LIKE :search')
            ->andWhere('x.id  NOT IN (:ids)')
            ->setParameter('search', '%'.$searchValue.'%')
            ->setParameter('ids', $exclude)
            ->getQuery()
            ->getArrayResult()

        ;

        $q = $this->getDoctrine()->getRepository($entity)->createQueryBuilder('x')
            ->select('x.id , x.'.$field)
            ->where('x.'.$field.' LIKE :search')
            ->setParameter('search', '%'.$searchValue.'%')
        ;
        if($exclude && count($exclude)>0){
            $q->andWhere('x.id  NOT IN (:ids)')
                ->setParameter('ids', $exclude)
            ;
        }
        $results = $q->getQuery()->setMaxResults(20)->getArrayResult();

        if ($request->isXMLHttpRequest()) {
            return new JsonResponse(array('data' => $results));
        }
        return null;
    }

    public function liveEditorListAction(Request $request)
    {
        $id = $request->get('id');
        $entityName = $request->get('entityName');
        $action = $request->get('action');

        if($id && $entityName && $action){

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($entityName)->find($id);


            if($action == "remove"){
                $em->remove($entity);
                $em->flush();
                return new Response("ok");
            }

            if($action == "publish"){
                $entity->setIsPublished(1);
                $em->persist($entity);
                $em->flush();
                return $this->render("AppBundle:LiveEditorList:isPublishedRow.html.twig", array(
                    'isPublished'=>1,
                    'id'=>$id
                ));
            }

            if($action == "unpublish"){
                $entity->setIsPublished(0);
                $em->persist($entity);
                $em->flush();
                return $this->render("AppBundle:LiveEditorList:isPublishedRow.html.twig", array(
                    'isPublished'=>0,
                    'id'=>$id
                ));
            }



        }else{
            return new Response("ko");
        }

    }



}
