<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class PictureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('pictureFile',FileType::class,array(
                'label'=>'storebundle.pictures',
                'required'=>true,
                "attr" => array(
                    "accept" => "image/*",
                   # "multiple" => "multiple",
                )
            ))
            ->add('pos',null,array(
                'label'=>'storebundle.picture'
            ))
            ->add('description',array(
                'label'=>'storebundle.picture',
                'attr'=>array(
                    'class'=>'editor-text'
                )

            ))
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Picture'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_picture';
    }


}
