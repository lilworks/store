<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ShippingMethodsCountriesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingMethod', EntityType::class, array(
                'label'=>'storebundle.shippingmethod',
                'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false
            ))
            ->add('price',MoneyType::class,array(
                'label'=>'storebundle.price',
                'required'=>false,
                'empty_data'=>''
            ))
            ->add('freeTrigger',MoneyType::class,array(
                'label'=>'storebundle.shippingmethod.freetrigger',
                'required'=>false,
                'empty_data'=>''
            ))
            ->add('delay',null,array(
                'label'=>'storebundle.shippingmethod.delay'
            ))
            ->add('priority',null,array(
                'label'=>'storebundle.shippingmethod.priority'
            ))

        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\ShippingMethodsCountries'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_shippingmethodscountries';
    }


}
