<?php

namespace LilWorks\StoreBundle\Entity\Repository;

/**
 * DepositSaleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShippingMethodsCountriesTriggersRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPriceByTot( $shippingMethodCountry , $tot){

        // First check in country
        $qb = $this->createQueryBuilder('smct');
        $qb
            ->select('smct.price')
            ->join('smct.shippingMethodCountry','smc')
            ->leftJoin('smc.shippingMethod','sm')
             ->where('smc.id = :smc')
             ->andWhere('smct.trigger <= :tot')
             ->andWhere('smc.isPublished = 1')
            ->andWhere('sm.isPublished = 1')
             ->setParameter('smc',$shippingMethodCountry->getId())
             ->setParameter('tot',$tot)
             ->setMaxResults(1)
             ->orderBy('smct.trigger','desc')
        ;

        if($result = $qb->getQuery()->getOneOrNullResult()){
            return $result['price'];
        }else{ // GET THE DEFAULT PRICE

            $qb = $this->createQueryBuilder('smct');
            $qb
                ->select('smc.price')
                ->leftJoin('smct.shippingMethodCountry','smc')
                ->leftJoin('smc.shippingMethod','sm')
                ->where('smc.id = :smc')
                ->andWhere('smc.isPublished = 1')
                ->andWhere('sm.isPublished = 1')
                ->setParameter('smc',$shippingMethodCountry->getId())
                ->setMaxResults(1)
            ;

            if($result = $qb->getQuery()->getOneOrNullResult())
                return $result['price'];

        }


        $qb = $this->createQueryBuilder('smct');
        $qb
            ->select('smt.id,smt.price ')
            ->join('smct.shippingMethodCountry','smc')
            ->leftJoin('smc.shippingMethod','sm')
            ->leftJoin('sm.triggers','smt')
            ->where('sm = :sm')
            ->andWhere('smt.trigger <= :tot')
            ->andWhere('smc.isPublished = 1')
            ->andWhere('sm.isPublished = 1')
            ->setParameter('sm',$shippingMethodCountry->getShippingMethod()->getId())
            ->setParameter('tot',0)
            ->setMaxResults(1)
            ->orderBy('smt.trigger','desc')
        ;
        ;


        if($result = $qb->getQuery()->getOneOrNullResult()){
            return $result['price'];
        }else{ // GET THE DEFAULT PRICE
            $result = $this->getEntityManager()->getRepository('LilWorksStoreBundle:ShippingMethod')->find($shippingMethodCountry->getShippingMethod()->getId());
            if($result)
                return $result->getPrice();

        }


        return null;
    }
}
