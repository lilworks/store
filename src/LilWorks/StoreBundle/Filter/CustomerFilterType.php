<?php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class CustomerFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', Filters\TextFilterType::class,array(
                'label'=>'storebundle.firstname'
            ))
            ->add('lastName', Filters\TextFilterType::class,array(
                'label'=>'storebundle.lastname'
            ))
            ->add('companyName', Filters\TextFilterType::class,array(
                'label'=>'storebundle.companyname'
            ))
        ;
        $builder->add('phonenumbers', Filters\CollectionAdapterFilterType::class, array(
            'label'=>'storebundle.phonenumbers',
            'entry_type' => PhonenumberFilterType::class,
            'add_shared' => function (FilterBuilderExecuterInterface $qbe)  {
                $closure = function (QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
                    // add the join clause to the doctrine query builder
                    // the where clause for the label and color fields will be added automatically with the right alias later by the Lexik\Filter\QueryBuilderUpdater
                    $filterBuilder->leftJoin($alias . '.phonenumbers', $joinAlias);
                };

                // then use the query builder executor to define the join and its alias.
                $qbe->addOnce($qbe->getAlias().'.phonenumbers', 'cp', $closure);
            },
        ));
        $builder->add('addresses', Filters\CollectionAdapterFilterType::class, array(
            'label'=>'storebundle.addresses',
            'entry_type' => AddressFilterType::class,
            'add_shared' => function (FilterBuilderExecuterInterface $qbe)  {
                $closure = function (QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
                    // add the join clause to the doctrine query builder
                    // the where clause for the label and color fields will be added automatically with the right alias later by the Lexik\Filter\QueryBuilderUpdater
                    $filterBuilder->leftJoin($alias . '.addresses', $joinAlias);
                };

                // then use the query builder executor to define the join and its alias.
                $qbe->addOnce($qbe->getAlias().'.addresses', 'ca', $closure);
            },
        ));
    }

    public function getBlockPrefix()
    {
        return 'customer_filter';
    }


}