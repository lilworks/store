<?php
// ItemFilterType.php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;

use Lexik\Bundle\FormFilterBundle\Filter\Condition\ConditionBuilderInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class OrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', Filters\TextFilterType::class,array(
                'label'=>'storebundle.reference'
            ))
        ;

        $builder->add('customer', Filters\CollectionAdapterFilterType::class, array(
            'label'=>'storebundle.category',
            'entry_type' => CustomerForOrderFilterType::class,
            'add_shared' => function (FilterBuilderExecuterInterface $qbe)  {
                $closure = function (QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
                    $filterBuilder->leftJoin($alias . '.customer', $joinAlias);
                };
                $qbe->addOnce($qbe->getAlias().'.customer', 'c', $closure);

            },
        ));

    }

    public function getBlockPrefix()
    {
        return 'order_filter';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') ,
           /* 'filter_condition_builder' => function (ConditionBuilderInterface $builder) {
                    $builder
                        ->root('or')
                        ->field('customer.firstName')
                        ->orX()
                        ->field('customer.lastName')
                        ->orX()
                        ->field('customer.companyName')
                        ->end()
                        ->field('reference')
                        ->end()
                    ;
                }*/
        ));
    }
}