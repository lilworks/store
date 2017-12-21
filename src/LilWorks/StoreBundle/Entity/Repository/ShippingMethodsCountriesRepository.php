<?php

namespace LilWorks\StoreBundle\Entity\Repository;

/**
 * DepositSaleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShippingMethodsCountriesRepository extends \Doctrine\ORM\EntityRepository
{
    public function getShippingMethodsByCountry( $country ){


        $qb = $this->createQueryBuilder('smc');
        $qb
            ->select('smc')
            ->leftJoin('smc.shippingMethod','sm')
            ->leftJoin('smc.country','c')
            ->where('c.isPublished = 1 AND c.id = :cid')
            ->andWhere('smc.isPublished = 1')
            ->andWhere('sm.isPublished = 1')
            ->setParameter('cid',$country->getId())
        ;




        return $qb->getQuery()->getResult();

    }
}
