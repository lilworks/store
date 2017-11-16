<?php
// ItemFilterType.php
namespace LilWorks\StoreBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class PhonenumberFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phonenumber', Filters\TextFilterType::class,array(
            'label'=>'storebundle.phonenumber'
        ));

    }

    public function getBlockPrefix()
    {
        return 'phonenumber_filter';
    }
}