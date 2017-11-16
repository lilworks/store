<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Valid;

class ShippingMethodsCountriesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $shippingMethodCountry = $builder->getData();

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
            ->add('isPublished',null,array(
                'label'=>'storebundle.ispublished',
            ))
            ->add('price',MoneyType::class,array(
                'label'=>'storebundle.price',
                'required'=>false,
                'empty_data'=>''
            ))
            /*
            ->add('freeTrigger',MoneyType::class,array(
                'label'=>'storebundle.shippingmethod.freetrigger',
                'required'=>false,
                'empty_data'=>''
            ))
            */
            ->add('delay',null,array(
                'label'=>'storebundle.shippingmethod.delay'
            ))
            ->add('priority',null,array(
                'label'=>'storebundle.shippingmethod.priority'
            ))

        ;
        if($shippingMethodCountry && $shippingMethodCountry->getShippingMethod()){
            $builder->add('triggers', CollectionType::class, array(
                'constraints' => array(new Valid()),
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => ShippingMethodCountryTriggerType::class
            ))
            ;
        }
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
