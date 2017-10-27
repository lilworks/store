<?php

namespace LilWorks\StoreBundle\Form;


use LilWorks\StoreBundle\Entity\Tax;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use LilWorks\StoreBundle\Form\RealShippingMethodType;
use Doctrine\ORM\EntityRepository;

class OrdersProductsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $orderId=$options["orderId"];
        $context=$options["context"];



        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($orderId,$context) {
            $orderProduct = $event->getData();
            $form = $event->getForm();



            if($orderProduct){


                if($context == "online"){
                    $dataTax = $orderProduct->getProduct()->getTaxesOnline();
                    $dataWarranty = $orderProduct->getProduct()->getWarrantiesOnline();
                }else{
                    $dataTax = $orderProduct->getProduct()->getTaxesOffline();
                    $dataWarranty = $orderProduct->getProduct()->getWarrantiesOffline();
                }

                if(is_null($orderProduct->getIsSecondHand())){
                    $dataIsSecondHand = $orderProduct->getProduct()->getIsSecondHand();
                }else{
                    $dataIsSecondHand = $orderProduct->getIsSecondHand();
                }

                if(is_null($orderProduct->getPrice())){
                    $dataPrice = ($context == "online") ? $orderProduct->getProduct()->getPriceOnline() : $orderProduct->getProduct()->getPriceOffline();
                }else{
                    $dataPrice = $orderProduct->getPrice();
                }
                if(is_null($orderProduct->getName()) || $orderProduct->getName() == ""){
                    $dataName = $orderProduct->getProduct()->getBrand()->getName() . " " . $orderProduct->getProduct()->getName();
                }else{
                    $dataName = $orderProduct->getName() ;
                }

                if(is_null($orderProduct->getQuantity()) || $orderProduct->getQuantity() == 0){
                    $dataQuantity = 1;
                }else{
                    $dataQuantity = ($dataIsSecondHand)?1:$orderProduct->getQuantity();
                }


                $form

                    ->add('name',null,array(
                        'required'=>true,
                        'data'=>$dataName
                    ))
                    ->add('quantity',null,array(
                        'required'=>true,
                        'data' => $dataQuantity
                    ))
                    ->add('price',MoneyType::class,array(
                        'required'=>true,
                        'data'=>$dataPrice
                    ))
                    ->add('taxes', EntityType::class, array(
                        'class'    => 'LilWorksStoreBundle:Tax' ,
                        'choice_label' => function ($obj) {
                            if( $obj->getType() == "RATIO"){
                                return   $obj->getName() . " "  . $obj->getValue() ."%" ;
                            }elseif($obj->getType() == "VALUE"){
                                return   $obj->getName() . " "  . $obj->getValue() ."€" ;
                            }

                        },
                        'query_builder' => function (EntityRepository $er) use ($orderProduct,$context){
                            return $er->createQueryBuilder('t');
                            /*
                            if($context == "online"){
                                return $er->createQueryBuilder('t')
                                    ->innerJoin('t.productsOffline', 'p')
                                    ->where('p.id = :product_id')
                                    ->setParameter('product_id',$orderProduct->getProduct()->getId())
                                    ;
                            }else{
                                return $er->createQueryBuilder('t');
                            }
                            */

                        },
                        'data' => $dataTax,
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => true ,
                        'multiple' => true
                    ))
                    ->add('isSecondHand',null,array(
                        'data'=>$dataIsSecondHand,
                        'required'=>true,
                    ))
                    ->add('orderRealShippingMethod', EntityType::class, array(
                        'class'    => 'LilWorksStoreBundle:OrdersRealShippingMethods' ,
                        'choice_label' => function ($obj) {
                            return   $obj->getShippingMethod()->getName() . " " . $obj->getReference() ;
                        },
                        'query_builder' => function (EntityRepository $er) use ($orderId){
                            return $er->createQueryBuilder('orsm')
                                ->where('orsm.order = :id')
                                ->setParameter('id',$orderId)
                                ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => true ,
                        'multiple' => false
                    ))



                    ->add('serialNumber',null,array('required'=>false) )
                    ->add('warranties', EntityType::class, array(
                        'class'    => 'LilWorksStoreBundle:Warranty' ,
                        'choice_label' => function ($obj) { return   $obj->getName() ; },
                        'query_builder' => function (EntityRepository $er) use ($orderProduct){
                            return $er->createQueryBuilder('w')
                                #->innerJoin('t.productsOffline', 'p')
                                #->where('p.id = :product_id')
                                #->setParameter('product_id',$orderProduct->getProduct()->getId())
                                ;
                        },
                        'data' => $dataWarranty,
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => true ,
                        'multiple' => true
                    ));
            }else{
                $form
                    ->add('product', EntityType::class, array(
                        'class'    => 'LilWorksStoreBundle:Product' ,
                        'choice_label' => function ($obj) { return   $obj->getBrand()->getName() . " " . $obj->getName() . " (" . $obj->getStock() .")" ; },
                        'query_builder' => function (EntityRepository $er) use ($context){
                            $q = $er->createQueryBuilder('p')
                                ->leftJoin('LilWorksStoreBundle:Brand','b','WITH','b.id = p.brand')
                                ->where('p.isArchived != 1')
                                ->orderBy('b.name , p.name', 'ASC');
                            if($context == "online")
                                $q->where("p.isPublished = 1")
                                    ->andWhere('p.isArchived != 1');

                            return $q;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => false ,
                        'multiple' => false

                    ))

                ;
            }

        });

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\OrdersProducts',
            'orderId'=>null,
            'context'=>null,
            'cascade_validation' => true
        ));
    }
}
