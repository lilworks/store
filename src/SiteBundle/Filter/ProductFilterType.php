<?php
namespace SiteBundle\Filter;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class ProductFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Filters\TextFilterType::class)
            ->add('brandProductName', Filters\TextFilterType::class)
            ->add('isSecondHand', Filters\BooleanFilterType::class)
            ->add('brand', Filters\EntityFilterType::class, array(
                'class'    =>  'LilWorksStoreBundle:Brand',
                'choice_label' => function ($obj) { return   $obj->getName() ; },
            ))
            ;
        $builder->add('categories', Filters\CollectionAdapterFilterType::class, array(
            'entry_type' => CategoriesFilterType::class,
            'add_shared' => function (FilterBuilderExecuterInterface $qbe)  {
                $closure = function (QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
                    // add the join clause to the doctrine query builder
                    // the where clause for the label and color fields will be added automatically with the right alias later by the Lexik\Filter\QueryBuilderUpdater
                    $filterBuilder->leftJoin($alias . '.categories', $joinAlias);
                };
                // then use the query builder executor to define the join and its alias.
                $qbe->addOnce($qbe->getAlias().'.categories', 'opt', $closure);

            },
        ));
    }

    public function getBlockPrefix()
    {
        return 'product_filter';
    }

}