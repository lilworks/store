<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RealShippingMethodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('shippingMethod', EntityType::class, array(
                'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => false
            ))

            ->add('ordersProducts', EntityType::class, array(
                'class'    => 'LilWorksStoreBundle:OrdersProducts' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true
            ))
            ->add('shippedAt')
            ->add('receivedAt')
            ->add('reference')
            ->add('price',MoneyType::class)
            ->add('description')


        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\RealShippingMethod'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_realShippingmethod';
    }


}
