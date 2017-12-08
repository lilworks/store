<?php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
class OrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', Filters\TextFilterType::class,array(
                'label'=>'storebundle.reference'
            ))
        ;

        $builder->add('customer', Filters\EntityFilterType::class,array(
        'label'=>'storebundle.customer',
        'class'=>'LilWorksStoreBundle:Customer',
        'choice_label' => function ($obj) {
            return   $obj->getLastName() . " " .$obj->getFirstName() . " " . $obj->getCompanyName()  ;
        },
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('c')
                ->leftJoin('c.orders','o')
                ->where('o.id is not null')
                ->orderBy('c.lastName','asc')
                ->addOrderBy('c.firstName','asc');
        },
        'required' => false ,
        'expanded' => false ,
        'multiple' => false,
     /*   'attr' => array(
            'class'=>'selectpicker',
            'data-live-search'=>'true',
            'data-actions-box'=>true,
            'data-width'=>"300px"
        )*/
    ));
        $builder->add('product', Filters\EntityFilterType::class,array(
            'label'=>'storebundle.product',
            'class'=>'LilWorksStoreBundle:Product',
            'choice_label' => function ($obj) {
                return   $obj->getBrand()->getName() . " " .$obj->getName()   ;
            },
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->leftJoin('p.ordersProducts','op')
                    ->leftJoin('op.order','o')
                    ->leftJoin('p.brand','b')
                    ->where('o.id is not null')
                    ->andWhere('p.isArchived != 1 ')
                    ->orderBy('b.name','asc')
                    ->addOrderBy('p.name','asc');
            },
            'required' => false ,
            'expanded' => false ,
            'multiple' => false,
          /*  'attr' => array(
                'class'=>'selectpicker',
                'data-live-search'=>'true',
                'data-actions-box'=>true,
                'data-width'=>"300px"
            )*/
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
        ));
    }
}