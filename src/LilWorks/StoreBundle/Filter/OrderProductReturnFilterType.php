<?php
// ItemFilterType.php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\EntityRepository;

class OrderProductReturnFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', Filters\TextFilterType::class,array(
                'label'=>'storebundle.reference'
            ))
            ->add('customer', Filters\EntityFilterType::class,array(
                'label'=>'storebundle.customer',
                'class'=>'LilWorksStoreBundle:Customer',
                'choice_label' => function ($obj) {
                    return   $obj->getFirstName() . " " . $obj->getLastName(). " " . $obj->getCompanyName()  ;
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('c.orders','o')
                        ->leftJoin('o.ordersProducts','op')
                        ->leftJoin('op.orderProductReturn','opr')
                        ->where('opr.id is not null')

                        ;
                },
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'return_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}