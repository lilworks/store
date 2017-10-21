<?php

namespace LilWorks\StoreBundle\Form\Ajax\Brand;

use LilWorks\StoreBundle\Form\EventListener\IsSecondHandListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class IsPublishedType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('isPublished',ChoiceType::class,array(
                'choices' => array(
                    'no' => 0,
                    'yes' => 1
                ),
                'label' => false,'required' => true))
            #->add('isSecondHand',CheckboxType::class,array('label' => false,'required' => false,'empty_data' => 0))
            ->add('save', SubmitType::class, array(
            'attr' => array('class' => 'liveEditor_btn_save btn btn-success btn-sm'),
            ))
            ->add('cancel', ButtonType::class, array(
                'attr' => array('class' => 'liveEditor_btn_cancel btn btn-warning btn-sm'),
            ))

        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Brand'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_ajax_brand';
    }


}
