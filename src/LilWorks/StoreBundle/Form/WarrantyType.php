<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class WarrantyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array(
                'label'=>'storebundle.name',
            ))
            /*
            ->add('productsOnline', EntityType::class, array(
                'label'=>'storebundle.productsonline',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'choice_label' => function ($obj) {
                    $c = "";
                    foreach($obj->getCategories() as $category){
                        $c.=$category->getTag();
                    }
                    return    $obj->getBrand()->getName() ." ". $obj->getName() . ' (' .  $c . ')' ;
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->leftJoin('LilWorksStoreBundle:Brand','b','WITH','b.id = p.brand')
                        ->where('p.isPublished = 1 AND p.priceOnline > 0')
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
            ->add('productsOffline', EntityType::class, array(
                'label'=>'storebundle.productsoffline',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'choice_label' => function ($obj) {
                    $c = "";
                    foreach($obj->getCategories() as $category){
                        $c.=$category->getTag();
                    }
                    return    $obj->getBrand()->getName() ." ". $obj->getName() . ' (' .  $c . ')' ;
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->leftJoin('LilWorksStoreBundle:Brand','b','WITH','b.id = p.brand')
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
            ))*/
            ->add('descriptionPublic',null,array(
                'label'=>'storebundle.description',
                'attr' => ['class' => 'editor-text'],
            ))
            ->add('descriptionInternal',null,array(
                'label'=>'storebundle.descriptioninternal',
                'attr' => ['class' => 'editor-text'],
            ));


    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Warranty',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_warranty';
    }


}
