<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SuperCategoriesCategoriesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $superCategory = $options['superCategory'];


        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($superCategory) {
            $form = $event->getForm();

            if($superCategory->getId()){
                if($form->getData() && $form->getData()->getCategory()->getId()){
                    $category_id = $form->getData()->getCategory()->getId();
                    $form->add('category', EntityType::class, array(
                        'label'    => 'storebundle.category' ,
                        'class'    => 'LilWorksStoreBundle:Category' ,
                        'choice_label' => function ($obj) { return   $obj->getName() ; },
                        'query_builder' => function (EntityRepository $er) use ($category_id){

                            return $er->createQueryBuilder('c')
                                ->where('c.id = :category_id')
                                ->orderBy('c.name','asc')
                                ->setParameter('category_id',$category_id)
                                ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => false ,
                        'multiple' => false,
                        'attr' => array(
                            'class'=>'selectpicker',
                            'data-live-search'=>'true',
                            'data-actions-box'=>true,
                            'data-width'=>"300px"
                        )
                    ));
                }else{
                    $categoriesAdded = array();
                    foreach($superCategory->getSuperCategoriesCategories() as $scc){
                        array_push($categoriesAdded,$scc->getCategory()->getId());
                    }
                    $form->add('category', EntityType::class, array(
                        'label'    => 'storebundle.category' ,
                        'class'    => 'LilWorksStoreBundle:Category' ,
                        'choice_label' => function ($obj) { return   $obj->getName() ; },
                        'query_builder' => function (EntityRepository $er) use ($categoriesAdded) {

                            if(count($categoriesAdded)>0){
                                return $er->createQueryBuilder('c')
                                    ->where('c.id NOT IN (:categories)')
                                    ->orderBy('c.name','asc')
                                    ->setParameter('categories',$categoriesAdded)
                                    ;
                            }else{
                                return $er->createQueryBuilder('c')->orderBy('c.name','asc')

                                    ;
                            }

                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => false ,
                        'multiple' => false,
                        'attr' => array(
                            'class'=>'selectpicker',
                            'data-live-search'=>'true',
                            'data-actions-box'=>true,
                            'data-width'=>"300px"
                        )


                    ));
                }


            }else{
                    $form->add('category', EntityType::class, array(
                        'label'    => 'storebundle.category' ,
                        'class'    => 'LilWorksStoreBundle:Category' ,
                        'choice_label' => function ($obj) { return   $obj->getName() ; },
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('c') ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => false ,
                        'multiple' => false,
                        'attr' => array(
                            'class'=>'selectpicker',
                            'data-live-search'=>'true',
                            'data-actions-box'=>true,
                            'data-width'=>"300px"
                        )
                    ));


            }



        });
            $builder

            ->add('pos',null,array(
                'label'    => 'storebundle.pos' ,
            ))

        ;

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'superCategory'=>null,
            'data_class' => 'LilWorks\StoreBundle\Entity\SuperCategoriesCategories',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_supercategoriescategories';
    }


}
