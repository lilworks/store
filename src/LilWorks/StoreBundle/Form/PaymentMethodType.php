<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class PaymentMethodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array(
                'label'=>'storebundle.name',
            ))
            ->add('isPublished',null,array(
                'label'=>'storebundle.ispublished',
            ))
            ->add('description',null,array(
                'label'=>'storebundle.description',
            ))
            ->add('pictureFile',FileType::class,array(
                'label'=>'storebundle.picture',
                'required'=>false
            ))
            ->add('datas',null,array(
                'label'=>'storebundle.paymentmethod.data',
            ))
        ;






    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\PaymentMethod'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_paymentmethod';
    }


}
