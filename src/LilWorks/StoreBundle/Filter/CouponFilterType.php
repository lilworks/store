<?php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class CouponFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', Filters\TextFilterType::class,array(
                'label'=>'lilworks.storebundle.reference'
            ))
        ;
        $builder->add('customer', Filters\CollectionAdapterFilterType::class, array(
            'label'=>'lilworks.storebundle.customer',
            'entry_type' => CustomerForOrderFilterType::class,
            'add_shared' => function (FilterBuilderExecuterInterface $qbe)  {
                $closure = function (QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
                    // add the join clause to the doctrine query builder
                    // the where clause for the label and color fields will be added automatically with the right alias later by the Lexik\Filter\QueryBuilderUpdater
                    $filterBuilder->leftJoin($alias . '.customer', $joinAlias);
                };

                // then use the query builder executor to define the join and its alias.
                $qbe->addOnce($qbe->getAlias().'.customer', 'cu', $closure);
            },
        ));

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