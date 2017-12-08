<?php
namespace AppBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', Filters\TextFilterType::class,array(
                'label'=>'storebundle.username'
            ))
            ->add('email', Filters\TextFilterType::class,array(
                'label'=>'storebundle.email'
            ))
            ->add('customer', Filters\EntityFilterType::class,array(
                'label'=>'storebundle.customer',
                'class'=>'LilWorksStoreBundle:Customer',
                'choice_label' => function ($obj) {
                    return   $obj->getFirstName() . " " . $obj->getLastName() . " " . $obj->getCompanyName() ;
                }
            ))
        ;

    }

    public function getBlockPrefix()
    {
        return 'user_filter';
    }


}