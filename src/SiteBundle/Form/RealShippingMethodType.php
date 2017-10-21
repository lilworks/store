<?php
namespace SiteBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealShippingMethodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        $builder->add('price');

        $builder->add('shippingMethod', EntityType::class, array(
        'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
        'choice_label' => function ($obj) {
            return    $obj->getName() ;
        },
        'required' => false ,
        'mapped'=> true,
        'expanded' => true ,
        'multiple' => false
    ));*/
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\BasketsRealShippingMethods'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sitebundle_realshippingmethod';
    }

}