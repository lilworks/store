<?php
// ItemFilterType.php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class CountryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Filters\TextFilterType::class);
    }

    public function getParent()
    {
        return Filters\SharedableFilterType::class; // this allow us to use the "add_shared" option
    }
    public function getBlockPrefix()
    {
        return 'country_filter';
    }

}