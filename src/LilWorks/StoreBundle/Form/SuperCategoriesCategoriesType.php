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
                        'label'    => 'lilworks.storebundle.category' ,
                        'class'    => 'LilWorksStoreBundle:Category' ,
                        'choice_label' => function ($obj) { return   $obj->getName() ; },
                        'query_builder' => function (EntityRepository $er) use ($category_id){

                            return $er->createQueryBuilder('c')
                                ->where('c.id = :category_id')
                                ->setParameter('category_id',$category_id)
                                ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => false ,
                        'multiple' => false,
                        #'attr'=>['disabled'=>'disabled']
                    ));
                }else{
                    $categoriesAdded = array();
                    foreach($superCategory->getSuperCategoriesCategories() as $scc){
                        array_push($categoriesAdded,$scc->getCategory()->getId());
                    }
                    $form->add('category', EntityType::class, array(
                        'label'    => 'lilworks.storebundle.category' ,
                        'class'    => 'LilWorksStoreBundle:Category' ,
                        'choice_label' => function ($obj) { return   $obj->getName() ; },
                        'query_builder' => function (EntityRepository $er) use ($categoriesAdded) {

                            return $er->createQueryBuilder('c')
                                ->where('c.id NOT IN (:categories)')
                                ->setParameter('categories',$categoriesAdded)
                                ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => false ,
                        'multiple' => false
                    ));
                }


            }else{
                    $form->add('category', EntityType::class, array(
                        'label'    => 'lilworks.storebundle.category' ,
                        'class'    => 'LilWorksStoreBundle:Category' ,
                        'choice_label' => function ($obj) { return   $obj->getName() ; },
                        'query_builder' => function (EntityRepository $er) {

                            return $er->createQueryBuilder('c')

                                ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => false ,
                        'multiple' => false,
                    ));

            }



        });
            $builder

            ->add('pos',null,array(
                'label'    => 'lilworks.storebundle.pos' ,
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
