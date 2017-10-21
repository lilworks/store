<?php

namespace LilWorks\StoreBundle\Form\Ajax\Annonce;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pos',null,array('label' => false))
            ->add('save', SubmitType::class, array(
            'attr' => array('class' => 'ajaxEditor_btn_save btn btn-success btn-sm'),
            ))
            ->add('cancel', ButtonType::class, array(
                'attr' => array('class' => 'ajaxEditor_btn_cancel btn btn-warning btn-sm'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Annonce'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_ajax_annonce';
    }

}
