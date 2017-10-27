<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ShippingMethodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array(
                'label'=>'storebundle.name'
            ))
            ->add('isPublished',null,array(
                'label'=>'storebundle.ispublished'
            ))
            ->add('price',MoneyType::class,array(
                'label'=>'storebundle.price'
            ))
            ->add('freeTrigger',null,array(
                'label'=>'storebundle.shippingmethod.freetrigger'
            ))
            ->add('priority',null,array(
                'label'=>'storebundle.shippingmethod.priority'
            ))
            ->add('delay',null,array(
                'label'=>'storebundle.shippingmethod.delay'
            ))
            ->add('pictureFile',FileType::class,array(
                'label'=>'storebundle.picture',
                'required'=>false
            ))
            ->add('description',null,array(
                'label'=>'storebundle.description',
            ))
            ->add('descriptionInternal',null,array(
                'label'=>'storebundle.descriptioninternal',
            ));
        $builder->add('products', EntityType::class, array(
            'label'=>'storebundle.products',
            'class'    => 'LilWorksStoreBundle:Product' ,
            'required' => false ,
            'mapped'=> true,
            'expanded' => true ,
            'multiple' => true,
            'choice_label' => function ($obj) {
                return   $obj->getBrand()->getName() . " " . $obj->getName()   ;
            },
            'query_builder' => function (EntityRepository $er)  {
                return $er->createQueryBuilder('p')
                    ->leftJoin('LilWorksStoreBundle:Brand','b','WITH','b.id = p.brand')
                    ->where('p.isArchived != 1')
                    ->orderBy('b.name','asc')
                    ;
            },
            'mapped'=> true,
            'expanded' => false ,
            'multiple' => true,
            'attr' => array(
                'class'=>'selectpicker',
                'data-live-search'=>'true',
                'data-actions-box'=>true,
                'data-width'=>"300px"
            )

        ));

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\ShippingMethod'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_shippingmethod';
    }


}
