<?php

namespace LilWorks\StoreBundle\Entity\Repository;


/**
 * ShippingMethodRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
use Doctrine\ORM\Query\ResultSetMapping;

class ShippingMethodRepository extends \Doctrine\ORM\EntityRepository
{

    public function getProductShippingMethods($productId,$countryId){
        return $this->createQueryBuilder('sm')
            ->leftJoin('sm.products','p')
            ->leftJoin('sm.shippingmethods_countries','smc')
            ->leftJoin('smc.country','c')
            ->where('sm.isPublished = 1')
            ->andWhere('smc.isPublished = 1')
            ->andWhere('p.id = :product_id')
            ->andWhere('c.id = :country_id')
            ->setParameter('product_id',$productId)
            ->setParameter('country_id',$countryId)
            ->getQuery()
            ->getArrayResult()
            ;
    }
    public function getPriceInContext($smId,$tot,$countryId){

        $toto = "if(
          if(MAX(l4_.shippingMethodCountryTrigger),(SELECT price from lilworks_shipping_methods_countries_triggers WHERE shippingMethodCountry=l1_.id AND shippingMethodCountryTrigger=MAX(l4_.shippingMethodCountryTrigger) ),l1_.price) ,
            if(MAX(l4_.shippingMethodCountryTrigger),(SELECT price from lilworks_shipping_methods_countries_triggers WHERE shippingMethodCountry=l1_.id AND shippingMethodCountryTrigger=MAX(l4_.shippingMethodCountryTrigger) ),l1_.price),
            if(MAX(l5_.shippingMethodTrigger),(SELECT price from lilworks_shipping_methods_triggers WHERE shippingMethod=l0_.id AND shippingMethodTrigger=MAX(l5_.shippingMethodTrigger) ),l0_.price)
        ) as rrr";
        $sql = "
        SELECT
    l0_.id,
    IF(MAX(l4_.shippingMethodCountryTrigger),
        (SELECT
                price
            FROM
                lilworks_shipping_methods_countries_triggers
            WHERE
                shippingMethodCountry = l1_.id
                    AND shippingMethodCountryTrigger = MAX(l4_.shippingMethodCountryTrigger)),
        IF(l1_.price,
            l1_.price,
            IF(MAX(l5_.shippingMethodTrigger),
                (SELECT
                        price
                    FROM
                        lilworks_shipping_methods_triggers
                    WHERE
                        shippingMethod = l0_.id
                            AND shippingMethodTrigger = MAX(l5_.shippingMethodTrigger)),
                IF(l0_.price, l0_.price, NULL)))) AS selectedPrice
FROM
    lilworks_shipping_method l0_
        LEFT JOIN
    lilworks_shipping_methods_triggers l5_ ON l5_.shippingMethod = l0_.id
        AND l5_.shippingMethodTrigger <= :tot
        LEFT JOIN
    lilworks_shipping_methods_countries l1_ ON l0_.id = l1_.shippingMethod
        LEFT JOIN
    lilworks_country l2_ ON l1_.country = l2_.id
        LEFT JOIN
    lilworks_shipping_methods_countries_triggers l4_ ON l1_.id = l4_.shippingMethodCountry
        AND l4_.shippingMethodCountryTrigger <= :tot
WHERE
    l0_.isPublished = 1 AND l0_.id = :sm_id
        AND l1_.isPublished = 1
        AND l2_.id = :country_id
GROUP BY l1_.id
;
        ";

        $em = $this->getEntityManager();
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('selectedPrice', 'selectedPrice');

        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter('tot', $tot);
        $query->setParameter('country_id', $countryId);
        $query->setParameter('sm_id', $smId);
        return $query->getOneOrNullResult();

    }
}