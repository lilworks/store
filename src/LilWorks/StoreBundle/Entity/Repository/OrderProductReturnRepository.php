<?php

namespace LilWorks\StoreBundle\Entity\Repository;

/**
 * CouponRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderProductReturnRepository extends \Doctrine\ORM\EntityRepository
{
    public function getNextReference($orderProductReturn){

        $prefix = "RC";


        $year = ($orderProductReturn->getReturnedAt()) ? $orderProductReturn->getReturnedAt()->format('Y') : date('Y') ;

        $qb = $this->createQueryBuilder('opr');
        $qb
            ->select('opr.reference')
            ->where('opr.reference LIKE :filter')
            ->setParameter('filter', $year.'-'.$prefix.'%');

        $results = $qb->getQuery()->getScalarResult();
        if(count($results)>0){
            $values = array();
            foreach($results as $result){
                array_push($values,intval(str_replace( $year.'-'.$prefix ,"",$result["reference"] ))) ;
            }
            rsort($values);
            $nextIndex = $values[0]+1;
        }else{
            $nextIndex = 1;
        }

        $zeroLeft = 4 - count($nextIndex);
        for($i=0;$i<$zeroLeft;$i++){
            $nextIndex =  "0" . $nextIndex;
        }

        return "$year-$prefix$nextIndex";

    }
}
