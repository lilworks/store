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
                'label'=>'lilworks.storebundle.name',
            ))
            ->add('isPublished',null,array(
                'required'=>true,
                'label'=>'lilworks.storebundle.ispublished',
            ))
            ->add('website',UrlType::class,array(
                'label'=>'lilworks.storebundle.brand.website',
                'required'=>false
            ))
            ->add('pictureFile',FileType::class,array(
                'label'=>'lilworks.storebundle.picture',
                'required'=>false
            ))
            ->add('products', EntityType::class, array(
                'label'=>'lilworks.storebundle.products',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'choice_label' => function ($obj) { return    $obj->getName() ; },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
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
                'label'=>'lilworks.storebundle.description',
                'attr' => ['class' => 'text-editor'],
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
        return 'lilworks_storebundle_brand';
    }


}
