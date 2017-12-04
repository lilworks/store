<?php

namespace SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BasketRealShippingMethodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingMethod', EntityType::class, array(
                'label'=>'sitebundle.shippingmethod',
                'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'required' => true ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false
            ))
            ;
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
        return 'lilworks_storebundle_shippingmethod';
    }


}
