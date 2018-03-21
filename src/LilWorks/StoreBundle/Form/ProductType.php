<?php

namespace LilWorks\StoreBundle\Form;

use LilWorks\StoreBundle\Entity\Picture;
use LilWorks\StoreBundle\Form\Transformer\PicturesTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $product = $builder->getData();


        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {


            $product = $event->getData();
            $form = $event->getForm();



            foreach($form->get("multiplepictures")->getData() as $manualPicture) {
                //$product->addPicture();
                $picture = new Picture();
                $picture->setPictureFile($manualPicture);
                $picture->setPictureName($manualPicture->getClientOriginalName());
                $picture->setProduct($product);
                $product->addPicture($picture);
            }






        });


        $builder
            ->add('name',null,array(
                'label'=>'storebundle.name',
            #    'validation_groups'=>array('general')
            ))
            ->add('isPublished',null,array(
                'label'=>'storebundle.ispublished',
            ))
            ->add('isArchived',null,array(
                'label'=>'storebundle.isarchived',
            ))
            ->add('isSecondHand',null,array(
                'label'=>'storebundle.product.issecondhand',
            ))
            ->add('brand', EntityType::class, array(
                'label'=>'storebundle.brand',
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
                'label'=>'storebundle.categories',
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
/*
            ->add('relatedProducts', EntityType::class, array(
                'label'=>'storebundle.product.relatedproducts',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'choice_label' => function ($obj) {
                    $strCat = "";
                    foreach($obj->getCategories() as $category){
                        $strCat.= " " . $category->getName();
                    }
                    return  $obj->getBrand()->getName() . " " . $obj->getName() . " ($strCat )" ;
                },
                'query_builder' => function (EntityRepository $er) use ($product)  {
                    if($product && $product->getId()>0){
                        return $er->createQueryBuilder('rp')
                            ->leftJoin('LilWorksStoreBundle:Brand','b','WITH','b.id = rp.brand')
                            ->where('b.id != :id')
                            ->andWhere('rp.isArchived != 1')
                            ->setParameter('id',$product->getId())
                            ->orderBy('b.name','asc')
                            ;
                    }
                    return $er->createQueryBuilder('rp')
                        ->leftJoin('LilWorksStoreBundle:Brand','b','WITH','b.id = rp.brand')
                        ->where('rp.isArchived != 1')
                        ->orderBy('b.name','asc')
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
*/

            ->add('description',null,array(
                'label'=>'storebundle.description',
                'attr' => ['class' => 'editor-text'],
            ))
            ->add('descriptionInternal',null,array(
                'label'=>'storebundle.descriptioninternal',
                'attr' => ['class' => 'editor-text'],
            ))

            ->add('priceOffline',MoneyType::class,array(
                'label'=>'storebundle.product.priceoffline',
                'required'=>true
            ))
            ->add('taxesOffline', EntityType::class, array(
                'label'=>'storebundle.product.taxesoffline',
                'class'    => 'LilWorksStoreBundle:Tax' ,
                'choice_label' => function ($obj) { return   $obj->getName() . ' | ' . $obj->getValue(); },
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true
            ))

            ->add('priceOnline',MoneyType::class,array(
                'label'=>'storebundle.product.priceonline',
                #'validation_groups'=>array('prices')
            ))
            ->add('taxesOnline', EntityType::class, array(
                'label'=>'storebundle.product.taxesonline',
                'class'    => 'LilWorksStoreBundle:Tax' ,
                'choice_label' => function ($obj) { return   $obj->getName() . ' | ' . $obj->getValue(); },
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true,
                'validation_groups'=>array('prices')
            ))

            ->add('priceBuying',MoneyType::class,array(
                'label'=>'storebundle.product.pricebuying',
                'required'=>false
            ))
            ->add('priceRetail',MoneyType::class,array(
                'label'=>'storebundle.product.priceretail',
                'required'=>false
            ))

            ->add('warrantiesOffline', EntityType::class, array(
                'label'=>'storebundle.product.warrantiesoffline',
                'class'    => 'LilWorksStoreBundle:Warranty' ,
                'choice_label' => function ($obj) { return   $obj->getName();},
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true
            ))

            ->add('warrantiesOnline', EntityType::class, array(
                'label'=>'storebundle.product.warrantiesonline',
                'class'    => 'LilWorksStoreBundle:Warranty' ,
                'choice_label' => function ($obj) { return   $obj->getName();},
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true
            ))
            ->add('shippingMethods', EntityType::class, array(
                'label'=>'storebundle.product.shippingmethods',
                'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
                'choice_label' => function ($obj) { return   $obj->getName();},
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => true,

            ));

        $builder->add('pictures', CollectionType::class, array(
                'label'=>'storebundle.pictures',
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => PictureType::class,

        ))
            #->addModelTransformer(new PicturesTransformer())
        ;

        $builder
            ->add('multiplepictures', FileType::class, [
                'label'=>'storebundle.pictures',
                'help'=>'storebundle.help.pictures.multiple',
                'mapped'=>false,
                'multiple' => true,
                'required' => false,
                'attr'     => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ]
            ])
        ;

            $builder->add('tags', EntityType::class, array(
                'label'=>'storebundle.product.tags',
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
                'label'=>'storebundle.product.weight',
            ))
            ->add('length',null,array(
                'label'=>'storebundle.product.length',
            ))
            ->add('width',null,array(
                'label'=>'storebundle.product.width',
            ))
            ->add('height',null,array(
                'label'=>'storebundle.product.height',
            ))

                ->add('docfiles', EntityType::class, array(
                    'label'=>'storebundle.product.docfiles',
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
                'label'=>'storebundle.stock',
                'required'=>false
            ))
            ->add('leadTime',null,array(
                'label'=>'storebundle.product.leadtime',
                'required'=>false
            ))
            ->add('alwaysAvailable',null,array(
                'label'=>'storebundle.product.isalwaysavailable',
                'required'=>false
            ))
            ->add('isReviewable',null,array(
                'label'=>'storebundle.product.isreviewable',
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
