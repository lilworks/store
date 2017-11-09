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
                    return   $obj->getLastName() . " " .$obj->getFirstName() . " " . $obj->getCompanyName()  ;
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('c.depositSales','ds')
                        ->where('ds.id is not null')
                        ->orderBy('c.lastName','asc')
                        ->addOrderBy('c.firstName','asc');
                },
                'required' => false ,
                'expanded' => false ,
                'multiple' => false,
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px"
                )
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
                        ->leftJoin('p.brand','b')
                        ->where('ds.id is not null')
                        ->orderBy('b.name','asc')
                        ->addOrderBy('p.name','asc');
                },
                'required' => false ,
                'expanded' => false ,
                'multiple' => false,
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px"
                )
            ))


        ;

    }

    public function getBlockPrefix()
    {
        return 'depositsale_filter';
    }


}