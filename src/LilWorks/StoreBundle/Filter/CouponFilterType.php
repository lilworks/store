<?php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class CouponFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', Filters\TextFilterType::class,array(
                'label'=>'storebundle.reference'
            ))
            ->add('isSplitable', Filters\BooleanFilterType::class,array(
                'label'=>'storebundle.coupon.issplitable'
            ))
            ->add('isAvailableOnline', Filters\BooleanFilterType::class,array(
                'label'=>'storebundle.coupon.isavailableonline'
            ))
            ->add('isActive', Filters\BooleanFilterType::class,array(
                'label'=>'storebundle.coupon.isactive'
            ))
            ->add('validity', Filters\DateRangeFilterType::class,array(
                'label'=>'storebundle.coupon.validity'
            ))

            ->add('customer', Filters\EntityFilterType::class,array(
            'label'=>'storebundle.customer',
            'class'=>'LilWorksStoreBundle:Customer',
            'choice_label' => function ($obj) {
                return   $obj->getLastName() . " " .$obj->getFirstName() . " " . $obj->getCompanyName()  ;
            },
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->leftJoin('LilWorksStoreBundle:Coupon','co','WITH','co.customer = c.id')
                    ->where('co.id is not null')
                    ->orderBy('c.lastName','asc')
                    ->addOrderBy('c.firstName','asc')
                    ;
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
        return 'coupon_filter';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }

}