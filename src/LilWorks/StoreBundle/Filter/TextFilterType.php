<?php
// ItemFilterType.php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class TextFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Filters\TextFilterType::class,array(
                'label'=>'lilworks.storebundle.name'
            ))
            ->add('title', Filters\TextFilterType::class,array(
                'label'=>'lilworks.storebundle.title'
            ))
            ->add('isContent', Filters\BooleanFilterType::class,array(
                'label'=>'lilworks.storebundle.text.iscontent'
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'text_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}