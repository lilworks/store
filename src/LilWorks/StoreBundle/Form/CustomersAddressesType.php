<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CustomersAddressesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
/*
            ->add('address', EntityType::class, array(
                'class'    => 'LilWorksStoreBundle:Address' ,
                'choice_label' => function ($obj) {
                        return
                            $obj->getNumber() ." " .
                            $obj->getStreet(). " ".
                            $obj->getCity()
                        ; },
                'required' => true ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false
            ))
*/
            ->add('address', CollectionType::class, array(
                'mapped'=>true,
                'allow_add'=>true,
                'required' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => AddressType::class
            ))
            ->add('isDefaultShipping')
            ->add('isDefaultBill');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\CustomersAddresses'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_customersAddresses';
    }


}
