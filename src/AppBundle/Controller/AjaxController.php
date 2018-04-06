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

    public function getCountsAction(Request $request)
    {
        $entity = $request->get('entity');
        $child = $request->get('child');
        $childEntity = $request->get('childEntity');
        $id = $request->get('id');

        $currentCount =  $this->getDoctrine()->getRepository($entity)->createQueryBuilder('x')
            ->select('count(c.id) as currentCount')
            ->leftJoin('x.'.$child , 'c')
            ->where('x.id = :id')
            ->andWhere('c.isArchived != 1')

            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $totCount =  $this->getDoctrine()->getRepository($childEntity)->createQueryBuilder('x')
            ->select('count(x.id) as totCount')
            ->where('x.isArchived != 1')
            ->getQuery()
            ->getOneOrNullResult()
        ;


        return new JsonResponse( array(
            'currentCount'=>  $currentCount["currentCount"],
            'availableCount'=>  intval($totCount["totCount"]) - intval($currentCount["currentCount"]),
        ));

    }
    public function addAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $request->get('entity');
        $child = $request->get('child');
        $childEntity = $request->get('childEntity');
        $childMethod = $request->get('childMethod');
        $id = $request->get('id');


        $resultsCurrents = $this->getDoctrine()->getRepository($entity)->createQueryBuilder('x')
            ->select('x.id as xId ,c.id as cId')
            ->leftJoin('x.'.$child , 'c')
            ->where('x.id = :id')
            ->andWhere('c.id is not null')
            ->andWhere('c.isArchived != 1')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult()
        ;


        if(count($resultsCurrents)>0){
            $currents = array();
            foreach($resultsCurrents as $current){
                array_push($currents,$current['cId']);
            }
        }


        $q = $this->getDoctrine()->getRepository($childEntity)->createQueryBuilder('x')
            ->select('x')
            ->leftJoin('x.brand','b')
            ->leftJoin('x.categories','cat')
            ->where('x.isArchived != 1')
            ->groupBy('x.id')
        ;

        if(isset($currents) > 0){
            $q->andWhere('x.id not in (:currents)')->setParameter('currents', $currents);
        }
        $resultsAvailables = $q->getQuery()->getResult();

        $parent =$this->getDoctrine()->getRepository($entity)->find($id);
        foreach($resultsAvailables as $result){
            $funcChild = 'add'.ucfirst($childMethod);
            $result->$funcChild($parent);
            $em->persist($result);
        }
        $em->flush();
        return new JsonResponse();
    }
    public function removeAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $childEntity = $request->get('childEntity');
        $parentEntity = $request->get('entity');
        $childMethod = $request->get('childMethod');
        $id = $request->get('id');
        $child = $request->get('child');

        $objects =  $this->getDoctrine()->getRepository($parentEntity)->createQueryBuilder('x')
            ->select('x.id as xId ,c.id as cId')
            ->leftJoin('x.'.$child,'c')
            ->where('c.isArchived != 1')
            ->andWhere('x.id = :id')
            ->setParameter('id',$id)
            ->groupBy('c.id')
            ->getQuery()
            ->getArrayResult()
        ;

        $parent =$this->getDoctrine()->getRepository($parentEntity)->find($id);

        foreach($objects as $object){
            $child =$this->getDoctrine()->getRepository($childEntity)->find($object['cId']);
            $funcChild = 'remove'.ucfirst($childMethod);
            $child->$funcChild($parent);
            $em->persist($child);
        }

        $em->flush();
        return new JsonResponse();
    }
    public function addChildAction(Request $request)
    {
       $em = $this->getDoctrine()->getManager();

        $childEntity = $request->get('childEntity');
        $parentEntity = $request->get('entity');

        $parentId = $request->get('id');
        $childMethod = $request->get('childMethod');
        $datas = $request->get('datas');


        foreach(json_decode($datas) as $data){

            $child = $em->getRepository($childEntity)->find($data);
            $parent = $em->getRepository($parentEntity)->find($parentId);
            $funcChild = 'add'.ucfirst($childMethod);
            $child->$funcChild($parent);
            $em->persist($child);

        }
        $em->flush();

        return new JsonResponse();
    }
    public function removeChildAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $childEntity = $request->get('childEntity');
        $parentEntity = $request->get('entity');

        $parentId = $request->get('id');
        $childMethod = $request->get('childMethod');
        $datas = $request->get('datas');


        foreach(json_decode($datas) as $data){

            $child = $em->getRepository($childEntity)->find($data);
            $parent = $em->getRepository($parentEntity)->find($parentId);
            $funcChild = 'remove'.ucfirst($childMethod);
            $child->$funcChild($parent);
            $em->persist($child);

        }

        $em->flush();

        return new JsonResponse();
    }
    public function searchCurrentChildAction(Request $request)
    {

        $entity = $request->get('entity');
        $child = $request->get('child');
        $childEntity = $request->get('childEntity');
        $id = $request->get('id');
        $maxResults = $request->get('maxResults');
        $searchString = $request->get('searchString');

        $resultsCurrents = $this->getDoctrine()->getRepository($entity)->createQueryBuilder('x')
            ->select('x.id as id,c.id as cId , b.name as brand , c.name as name , GROUP_CONCAT(ca.name) as categories ,CONCAT(b.name,c.name,GROUP_CONCAT(ca.name)) as searchString')
            ->leftJoin('x.'.$child , 'c')
            ->leftJoin('c.brand' , 'b')
            ->leftJoin('c.categories' , 'ca')
            ->where('x.id = :id')
            ->andWhere('c.isArchived != 1')
            ->having('searchString like :searchString')
            ->setParameter('id', $id)
            ->setParameter('searchString', "%".$searchString."%")
            ->setMaxResults($maxResults)
            ->groupBy('c.id')
            ->getQuery()
            ->getArrayResult()
        ;




        return new JsonResponse($resultsCurrents);
    }
    public function searchChildAction(Request $request){

        $entity = $request->get('entity');
        $child = $request->get('child');
        $childEntity = $request->get('childEntity');
        $id = $request->get('id');
        $maxResults = $request->get('maxResults');
        $searchString = $request->get('searchString');


        $resultsCurrents = $this->getDoctrine()->getRepository($entity)->createQueryBuilder('x')
            ->select('x.id as xId ,c.id as cId')
            ->leftJoin('x.'.$child , 'c')
            ->where('x.id = :id')
            ->andWhere('c.id is not null')
            ->andWhere('c.isArchived != 1')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult()
        ;


        if(count($resultsCurrents)>0){
            $currents = array();
            foreach($resultsCurrents as $current){
                array_push($currents,$current['cId']);
            }
        }


        $q = $this->getDoctrine()->getRepository($childEntity)->createQueryBuilder('x')
            ->select('x.id as id , b.name as brand , x.name as name , GROUP_CONCAT(cat.name) as categories ,CONCAT(b.name,x.name,GROUP_CONCAT(cat.name)) as searchString')
            ->leftJoin('x.brand','b')
            ->leftJoin('x.categories','cat')
            ->where('x.isArchived != 1')

            #->where('x.id not in (:currents)')
            ->having('searchString like :searchString')
            #->setParameter('currents', $currents)
            ->setParameter('searchString', "%".$searchString."%")
            ->groupBy('x.id')
            ->setMaxResults($maxResults)
           # ->getQuery()
           # ->getArrayResult()
        ;

        if(isset($currents) > 0){
            $q->andWhere('x.id not in (:currents)')->setParameter('currents', $currents);
        }
        $resultsAvailables = $q->getQuery()->getArrayResult();

        return new JsonResponse($resultsAvailables);

    }

    public function memorizedTabAction(Request $request){
        $session = $this->get('session');
        if($session->get('memorizedTabs/'.$request->request->get('tab'))){
            return new Response($session->get('memorizedTabs/'.$request->request->get('tab')));
        }
        return new Response();
    }

    public function memorizeTabAction(Request $request){
        $session = $this->get('session');
        $session->set('memorizedTabs/'.$request->request->get('tab'),$request->request->get('target'));
        return new Response();
    }


    public function postAcceptorAction(Request $request){
        /*******************************************************
         * Only these origins will be allowed to upload images *
         ******************************************************/
        $accepted_origins = array(
            "http://storeoffline",
            "http://localhost",
            "http://192.168.1.1",
            "http://new2.ferremusique.com",
            "http://www.ferremusique.com",
            "http://gestion"
        );

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
