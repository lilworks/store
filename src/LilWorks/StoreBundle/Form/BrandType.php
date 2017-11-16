<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
class BrandType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name',null,array(
                'required'=>true,
                'label'=>'storebundle.name',
            ))
            ->add('isPublished',null,array(
                #'required'=>true,
                'label'=>'storebundle.ispublished',
            ))
            ->add('website',UrlType::class,array(
                'label'=>'storebundle.brand.website',
                'required'=>false
            ))
            ->add('pictureFile',FileType::class,array(
                'label'=>'storebundle.picture',
                'required'=>false
            ))
            ->add('products', EntityType::class, array(
                'label'=>'storebundle.products',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'choice_label' => function ($obj) { return    $obj->getName() ; },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.isArchived != 1')
                        ->orderBy('p.name','asc')
                        ;
                },
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => true,
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px"
                )
            ))
            ->add('description',null,array(
                'label'=>'storebundle.description',
                'attr' => ['class' => 'editor-text'],
            ))
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Brand',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_brand';
    }


}
