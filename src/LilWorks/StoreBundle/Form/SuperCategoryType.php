<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SuperCategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $superCategory = $builder->getData();

        $builder
            ->add('name',null,array(
                'label'=>'storebundle.name'
            ))
            ->add('pos',null,array(
                'label'=>'storebundle.pos'
            ))
            ->add('isPublished',null,array(
                'label'=>'storebundle.ispublished'
            ))
            ->add('pictureFile',FileType::class,array(
                'label'=>'storebundle.picture',
                'required'=>false
            ))
            ->add('description',null,array(
                'label'=>'storebundle.description',
                'attr' => ['class' => 'editor-text'],
            ))
                ->add('superCategoriesCategories', CollectionType::class, array(
                    'label'=>'storebundle.categories',
                    'entry_options'=>array('superCategory'=>$superCategory),
                    'mapped'=>true,
                    'allow_add'=>true,
                    'required' => false,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'by_reference' => false,
                    'entry_type'   => SuperCategoriesCategoriesType::class,

            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\SuperCategory',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_supercategory';
    }


}
