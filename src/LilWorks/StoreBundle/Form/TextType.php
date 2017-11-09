<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class TextType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array(
                'label'=>'storebundle.text.name',
            ))
            ->add('title',null,array(
                'label'=>'storebundle.text.title',
            ))
            ->add('content',null,array(
                'label'=>'storebundle.text',
                'attr' => ['class' => 'editor-text'],
            ))
            ->add('css',null,array(
                'label'=>'storebundle.text.css',
                'attr' => ['class' => 'editor-css'],
            ))

            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Text'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_text';
    }


}
