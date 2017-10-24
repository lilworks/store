<?php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

/**
 * Embed filter type.
 */
class TagFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Filters\TextFilterType::class,array(
                'label'=>'storebundle.name'
            ));
    }

    public function getBlockPrefix()
    {
        return 'options_filter';
    }
}