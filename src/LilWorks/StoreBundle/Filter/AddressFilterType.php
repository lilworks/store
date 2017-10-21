<?php
// ItemFilterType.php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;



class AddressFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('street', Filters\TextFilterType::class,array(
            'label'=>'lilworks.storebundle.address.street'
        ));
        $builder->add('zipCode', Filters\TextFilterType::class,array(
            'label'=>'lilworks.storebundle.address.zipcode'
        ));
        $builder->add('city', Filters\TextFilterType::class,array(
            'label'=>'lilworks.storebundle.address.city'
        ));
        $builder->add('country', CountryFilterType::class, array(
            'label'=>'lilworks.storebundle.address.country',
            'add_shared' => function (FilterBuilderExecuterInterface $qbe) {
                $closure = function (QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
                    $filterBuilder->leftJoin($alias . '.country', $joinAlias);
                };

                $qbe->addOnce($qbe->getAlias().'.country', 'ac', $closure);
            }
        ));

    }

    public function getBlockPrefix()
    {
        return 'address_filter';
    }
}