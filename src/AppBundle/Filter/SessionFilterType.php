<?php
namespace AppBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class SessionFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\TextFilterType::class,array(
                'label'=>'appbundle.session.id'
            ))
            ->add('user', Filters\EntityFilterType::class,array(
                'label'=>'appbundle.customer',
                'class'=>'AppBundle:User',
                'choice_label' => function ($obj) {
                    return   $obj->getUsername();
                },
                'expanded' => false ,
                'multiple' => false,
             #   'attr' => array(
             #       'class'=>'selectpicker',
             #       'data-live-search'=>'true',
                    #'data-actions-box'=>true,
              #      'data-width'=>"300px"
               # )
            ))
        ;

    }

    public function getBlockPrefix()
    {
        return 'user_filter';
    }


}