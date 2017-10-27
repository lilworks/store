<?php
namespace LilWorks\StoreBundle\Filter;

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

        $builder->add('categories', Filters\CollectionAdapterFilterType::class, array(
            'label'=>'storebundle.category',
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

        $builder
            ->add('name', Filters\TextFilterType::class,array(
                'label'=>'storebundle.name'
            ))
            ->add('isPublished', Filters\BooleanFilterType::class,array(
                'label'=>'storebundle.ispublished'
            ))
            ->add('isSecondHand', Filters\BooleanFilterType::class,array(
                'label'=>'storebundle.product.issecondhand'
            ))
            ->add('isArchived', Filters\BooleanFilterType::class,array(
                'label'=>'storebundle.product.isarchived'
            ))
            ->add('brand', Filters\EntityFilterType::class, array(
                'label'=>'storebundle.brand',
                'class'    =>  'LilWorksStoreBundle:Brand',
                'choice_label' => function ($obj) { return   $obj->getName() ; },
            ));

    }

    public function getBlockPrefix()
    {
        return 'product_filter';
    }


}