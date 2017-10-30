<?php

namespace LilWorks\StoreBundle\Form\Ajax\RealShippingMethod;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ReceivedAtType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('receivedAt',DateTimeType::class,array(
            'required' => true,
            'attr'=>array('colType'=>'datetime'),
            'label' => false
        ))
            ->add('save', SubmitType::class,array(
                'label'=>'storebundle.save'
            ))
            ->add('cancel', ButtonType::class,array(
                'label'=>'storebundle.cancel'
            ))
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
        return 'lilworks_ajax_realshippingmethod';
    }


}
