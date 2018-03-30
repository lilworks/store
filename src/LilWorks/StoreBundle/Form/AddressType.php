<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array(
                'label'=>'storebundle.address.name',
            ))
            ->add('street',null,array(
                'label'=>'storebundle.address.street',
            ))
            ->add('complement',null,array(
                'label'=>'storebundle.address.complement',
            ))
            ->add('zipCode',null,array(
                'label'=>'storebundle.address.zipcode',
            ))
            ->add('city',null,array(
                'label'=>'storebundle.address.city',
            ))
            ->add('country', EntityType::class, array(
                'label'=>'storebundle.address.country',
                'class'    => 'LilWorksStoreBundle:Country' ,
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
            'data_class' => 'LilWorks\StoreBundle\Entity\Address',
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_address';
    }


}
