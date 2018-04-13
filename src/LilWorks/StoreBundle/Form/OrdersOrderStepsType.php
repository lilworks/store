<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
class OrdersOrderStepsType extends AbstractType
{
    private $orderManager;
    public function __construct($orderManager,$context,$mode){
        $this->orderManager = $orderManager;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $orderOrderStep = $builder->getData();

        $order  = $options["order"];
        $orderTypeTag = $order->getOrderType()->getTag();


        $allowedSteps = $this->orderManager->allowedSteps($order);
        $allowedStepsTag = array();
        foreach($allowedSteps as $orderStep){
            array_push($allowedStepsTag,$orderStep->getTag());
        }

        /*
        if($order->getId()){
            $builder->add('order',HiddenType::class,array(
                //'choice_label' => function ($obj) { return   $obj->getId() ; },
                'data'=>$order->getId()
            ));
        }*/
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($orderTypeTag,$allowedStepsTag) {
            $orderOrderStep = $event->getData();
            $form = $event->getForm();

            if(!is_null($orderOrderStep)){
                $form->add('orderStep', EntityType::class, array(
                    'label'=>'storebundle.orderstep',
                    'class'    => 'LilWorksStoreBundle:OrderStep' ,
                    'choice_label' => function ($obj) { return   $obj->getName() ; },
                    'required' => true ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'query_builder' => function (EntityRepository $er) use ($orderOrderStep) {
                        return $er->createQueryBuilder('os')
                            ->where('os.tag = :tag')->setParameter('tag',$orderOrderStep->getOrderStep()->getTag());
                    },
                ));
            }else{
                $form->add('orderStep', EntityType::class, array(
                    'label'=>'storebundle.orderstep',
                    'class'    => 'LilWorksStoreBundle:OrderStep' ,
                    'choice_label' => function ($obj) { return   $obj->getName() ; },
                    'required' => true ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'query_builder' => function (EntityRepository $er) use ($allowedStepsTag) {
                        return $er->createQueryBuilder('os')
                            ->where('os.tag in (:allowedTag)')
                            ->setParameter('allowedTag',$allowedStepsTag)
                            ;
                    },
                ));
            }
        });


        if($orderTypeTag == "FACTURE" || $orderTypeTag == "FACTURE_INTERNET" ){




        $builder
/*
            ->add('orderStep', EntityType::class, array(
                'label'=>'storebundle.orderstep',
                'class'    => 'LilWorksStoreBundle:OrderStep' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'required' => true ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) use ($allowedStepsTag) {
                        return $er->createQueryBuilder('os')
                            #->where('os.tag in (:allowedTag)')
                            #->setParameter('allowedTag',$allowedStepsTag)
                            ;
                },
            ))*/
            ->add('createdAt',null,array(
                'label'=>'storebundle.createdat',
                'required'=>false,
                'attr' => ['class' => 'datepicker'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yy',
                'required'=>false
            ))
            ->add('description',null,array(
                'label'=>'storebundle.description',
                'required'=>false
            ));
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\OrdersOrderSteps',
            'order'=>null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_ordersOrderSteps';
    }


}
