<?php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class CustomerForOrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', Filters\TextFilterType::class,array(
                'label'=>'lilworks.storebundle.firstname'
            ))
            ->add('lastName', Filters\TextFilterType::class,array(
                'label'=>'lilworks.storebundle.lastname'
            ))
            ->add('companyName', Filters\TextFilterType::class,array(
                'label'=>'lilworks.storebundle.companyname'
            ))
        ;

    }

    public function getBlockPrefix()
    {
        return 'customer_filter';
    }


}