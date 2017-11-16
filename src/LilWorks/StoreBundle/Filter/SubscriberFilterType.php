<?php
// ItemFilterType.php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\EntityRepository;

class SubscriberFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Filters\TextFilterType::class,array(
                'label'=>'storebundle.email'
            ))
            ->add('user', EntityType::class, array(
                'label'=>'storebundle.user',
                'class'    => 'AppBundle:User' ,
                'choice_label' => function ($obj) { return    $obj->getUsername()  ; },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ;
                },

                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px"
                )
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'subscriber_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}