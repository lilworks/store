<?php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\Query\Expr;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\EntityRepository;

class DepositSaleFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', Filters\TextFilterType::class,array(
                'label'=>'storebundle.reference'
            ))
            ->add('status', Filters\EntityFilterType::class,array(
                'label'=>'storebundle.depositsale.status',
                'class'=>'LilWorksStoreBundle:DepositSaleStatus',
                'choice_label' => function ($obj) {
                    return   $obj->getName() ;
                }
            ))
            ->add('customer', Filters\EntityFilterType::class,array(
                'label'=>'storebundle.customer',
                'class'=>'LilWorksStoreBundle:Customer',
                'choice_label' => function ($obj) {
                    return   $obj->getFirstName() . " " . $obj->getLastName(). " " . $obj->getCompanyName()  ;
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('LilWorksStoreBundle:DepositSale','ds','WITH','ds.customer = c.id')
                        ->where('ds.id is not null');
                },
            ))
            ->add('product', Filters\EntityFilterType::class,array(
                'label'=>'storebundle.product',
                'class'=>'LilWorksStoreBundle:Product',
                'choice_label' => function ($obj) {
                    return   $obj->getBrand()->getName() . " " . $obj->getName()  ;
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->leftJoin('LilWorksStoreBundle:DepositSale','ds','WITH','ds.product = p.id')
                        ->where('ds.id is not null')
                        ;
                },
            ))

        ;

    }

    public function getBlockPrefix()
    {
        return 'depositsale_filter';
    }


}