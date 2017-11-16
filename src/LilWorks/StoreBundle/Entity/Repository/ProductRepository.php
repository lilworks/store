<?php

namespace LilWorks\StoreBundle\Entity\Repository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository  extends \Doctrine\ORM\EntityRepository
{

    public function findForAll($search){

        $em = $this->getEntityManager();


        $qbc = $this->createQueryBuilder('p')
        ->select('p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('c.supercategories_categories', 'scc')
            ->leftJoin('scc.supercategory', 'sc')
            ->leftJoin('p.brand', 'b')
            ->andWhere('p.isPublished = 1');

        $values = explode(' ',$search);
        $c = 0;
        foreach($values as $value){
            if($c==0){
                $qbc
                    ->andWhere("p.name LIKE :value$c")
                    ->orWhere("b.name LIKE :value$c")
                    ->orWhere("c.name LIKE :value$c")
                    ->orWhere("sc.name LIKE :value$c")
                    ->setParameter('value'.$c, "%".$value."%")
                ;
            }else{
                $qbc
                    ->orWhere("p.name LIKE :value$c")
                    ->orWhere("b.name LIKE :value$c")
                    ->orWhere("c.name LIKE :value$c")
                    ->orWhere("sc.name LIKE :value$c")
                    ->setParameter('value'.$c, "%".$value."%")
                ;
            }
            $c++;
        }

        return $qbc->getQuery()->getResult();
        /*
         * $values = explode(' ',$values['value']);
                        $c = 0;
                        foreach($values as $value){
                            if($c==0){
                                $filterBuilder->getQueryBuilder()
                                    ->andWhere("p.name LIKE :value$c")
                                    ->orWhere("b.name LIKE :value$c")
                                    ->orWhere("c.name LIKE :value$c")
                                    ->orWhere("sc.name LIKE :value$c")
                                    ->setParameter('value'.$c, "%".$value."%")
                                ;
                            }else{
                                $filterBuilder->getQueryBuilder()
                                    ->orWhere("p.name LIKE :value$c")
                                    ->orWhere("b.name LIKE :value$c")
                                    ->orWhere("c.name LIKE :value$c")
                                    ->orWhere("sc.name LIKE :value$c")
                                    ->setParameter('value'.$c, "%".$value."%")
                                ;
                            }
                            $c++;
                        }
         */

    }
    public function findStrongestShippingMethod($id,$mode){

        $em = $this->getEntityManager();


        $dql = "
            SELECT sm FROM LilWorksStoreBundle:ShippingMethod sm
            JOIN sm.products p
            WHERE p.id = :id
            ORDER BY sm.force DESC
              ";

        $query = $em->createQuery($dql);
        $query->setParameter('id',$id);
        $query->setMaxResults(1);

        if($result = $query->getOneOrNullResult()){
            return $result;
        }else{
            return null;
        }
    }
}
