<?php
// ItemFilterType.php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\EntityRepository;

class CategoryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Filters\TextFilterType::class,array(
                'label'=>'lilworks.storebundle.name'
            ))
            ->add('isPublished', Filters\BooleanFilterType::class,array(
                'label'=>'lilworks.storebundle.ispublished'
            ))
            ->add('products', EntityType::class, array(
                'label'=>'lilworks.storebundle.products',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'choice_label' => function ($obj) { return    $obj->getBrand()->getName() ." ". $obj->getName() ; },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ;
                },

                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => true,
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
        return 'item_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}