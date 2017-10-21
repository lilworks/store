<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;
class ProductType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name',null,array(
                'label'=>'lilworks.storebundle.name',
            #    'validation_groups'=>array('general')
            ))
            ->add('isPublished',null,array(
                'label'=>'lilworks.storebundle.ispublished',
            ))
            ->add('isSecondHand',null,array(
                'label'=>'lilworks.storebundle.product.issecondhand',
            ))
            ->add('brand', EntityType::class, array(
                'label'=>'lilworks.storebundle.brand',
                'class'    => 'LilWorksStoreBundle:Brand' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'query_builder' => function (EntityRepository $er)   {
                        return $er->createQueryBuilder('b')
                            ->orderBy('b.name','asc')
                            ;
                },
                'required' => true ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px"
                )
            ))

            ->add('categories', EntityType::class, array(
                'label'=>'lilworks.storebundle.categories',
                'class'    => 'LilWorksStoreBundle:Category' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name','asc')
                        ;
                },
                'required' => true ,
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

            ->add('relatedProducts', EntityType::class, array(
                'label'=>'lilworks.storebundle.product.relatedproducts',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'choice_label' => function ($obj) { return  $obj->getBrand()->getName() . " " . $obj->getName() ; },
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('rp')
                        ->leftJoin('LilWorksStoreBundle:Brand','b','WITH','b.id = rp.brand')
                        ->orderBy('b.name','asc')
                        ;
                },
                'required' => true ,
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
            ->add('descriptionInternal',null,array(
                'label'=>'lilworks.storebundle.descriptioninternal',
                'attr' => ['class' => 'text-editor'],
            ))

            ->add('priceOffline',MoneyType::class,array(
                'label'=>'lilworks.storebundle.product.priceoffline',
                'required'=>true
            ))
            ->add('taxesOffline', EntityType::class, array(
                'label'=>'lilworks.storebundle.product.taxesoffline',
                'class'    => 'LilWorksStoreBundle:Tax' ,
                'choice_label' => function ($obj) { return   $obj->getName() . ' | ' . $obj->getValue(); },
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true
            ))

            ->add('priceOnline',MoneyType::class,array(
                'label'=>'lilworks.storebundle.product.priceonline',
                #'validation_groups'=>array('prices')
            ))
            ->add('taxesOnline', EntityType::class, array(
                'label'=>'lilworks.storebundle.product.taxesonline',
                'class'    => 'LilWorksStoreBundle:Tax' ,
                'choice_label' => function ($obj) { return   $obj->getName() . ' | ' . $obj->getValue(); },
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true,
                'validation_groups'=>array('prices')
            ))

            ->add('priceBuying',MoneyType::class,array(
                'label'=>'lilworks.storebundle.product.pricebuying',
                'required'=>false
            ))
            ->add('priceRetail',MoneyType::class,array(
                'label'=>'lilworks.storebundle.product.priceretail',
                'required'=>false
            ))

            ->add('warrantiesOffline', EntityType::class, array(
                'label'=>'lilworks.storebundle.product.warrantiesoffline',
                'class'    => 'LilWorksStoreBundle:Warranty' ,
                'choice_label' => function ($obj) { return   $obj->getName();},
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true
            ))

            ->add('warrantiesOnline', EntityType::class, array(
                'label'=>'lilworks.storebundle.product.warrantiesonline',
                'class'    => 'LilWorksStoreBundle:Warranty' ,
                'choice_label' => function ($obj) { return   $obj->getName();},
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true
            ))
            ->add('shippingMethods', EntityType::class, array(
                'label'=>'lilworks.storebundle.product.shippingmethods',
                'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
                'choice_label' => function ($obj) { return   $obj->getName();},
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true,

            ));

            $builder->add('pictures', CollectionType::class, array(
                'label'=>'lilworks.storebundle.pictures',
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => PictureType::class,

            ));








            $builder->add('tags', EntityType::class, array(
                'label'=>'lilworks.storebundle.product.tags',
                'class'    => 'LilWorksStoreBundle:Tag' ,
                'choice_label' => function ($obj) { return  $obj->getTag() ; },
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.tag','asc')
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
            ->add('weight',null,array(
                'label'=>'lilworks.storebundle.product.weight',
            ))
            ->add('length',null,array(
                'label'=>'lilworks.storebundle.product.length',
            ))
            ->add('width',null,array(
                'label'=>'lilworks.storebundle.product.width',
            ))
            ->add('height',null,array(
                'label'=>'lilworks.storebundle.product.height',
            ))







                ->add('docfiles', EntityType::class, array(
                    'label'=>'lilworks.storebundle.product.docfiles',
                    'class'    => 'LilWorksStoreBundle:Docfile' ,
                    'choice_label' => function ($obj) { return  $obj->getTitle() . " " . $obj->getDocName() ; },
                    'query_builder' => function (EntityRepository $er)  {
                        return $er->createQueryBuilder('df')
                            ->orderBy('df.title','asc')
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

            ->add('stock',null,array(
                'label'=>'lilworks.storebundle.stock',
                'required'=>false
            ))
            ->add('leadTime',null,array(
                'label'=>'lilworks.storebundle.product.leadtime',
                'required'=>false
            ))
            ->add('alwaysAvailable',null,array(
                'label'=>'lilworks.storebundle.product.isalwaysavailable',
                'required'=>false
            ))
            ->add('isReviewable',null,array(
                'label'=>'lilworks.storebundle.product.isreviewable',
                'required'=>false
            ))
        ;




    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Product',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_product';
    }


}
