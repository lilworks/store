<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Valid;

class OrderType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $order = $builder->getData();
        $customer = $builder->getData()->getCustomer();
        $context  = $options["context"];

        if($order->getOrderType()){
            $disabledOrderType = true;
        }else{
            $disabledOrderType = false;
        }

        if(!$order->getReference()){
            $dataReference = $options["orderUtils"]->setOrder($order)->getNextReference();
        }else{
            $dataReference = $order->getReference();
        }
        $haveDoneStep = $options["orderUtils"]->setOrder($order)->haveStep("DONE");
        $lastStep = $options["orderUtils"]->setOrder($order)->getLastStep();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($customer,$context) {
            $order = $event->getData();
            $form = $event->getForm();

            if($customer && count($customer->getAddresses())>0){
                $form->add('ordersRealShippingMethods', CollectionType::class, array(
                    'entry_options'  => array(
                        'context'  => $context,
                        'shippingAddress'=>($order->getShippingAddress())?$order->getShippingAddress():null
                    ),
                    'mapped'=>true,
                    'allow_add'=>true,
                    'required' => false,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'by_reference' => false,
                    'entry_type'   => OrderRealShippingMethodType::class
                ));

                $form->add('shippingAddress', EntityType::class, array(
                    'class'    => 'LilWorksStoreBundle:Address' ,
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'choice_label' => function ($obj) {
                        $address = "";
                        if($obj->getName())
                            $address.=$obj->getName() . " ";

                        $address.=$obj->getStreet() . ", ";

                        if($obj->getComplement())
                            $address.=$obj->getComplement() . ", ";

                        $address.=$obj->getZipCode() . " " . $obj->getCity() . ", " . $obj->getCountry()->getName()  ;
                        return    $address;
                    },
                    'query_builder' => function (EntityRepository $er) use ($customer) {
                        return $er->createQueryBuilder('a')
                            ->leftJoin('LilWorksStoreBundle:Customer','c','WITH','c.id = a.customer')
                            ->where('c.id = :customer_id')
                            ->setParameter('customer_id',$customer->getId())
                            ;
                    },
                ));
                $form->add('billingAddress', EntityType::class, array(
                    'class'    => 'LilWorksStoreBundle:Address' ,
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'choice_label' => function ($obj) {
                        $address = "";
                        if($obj->getName())
                            $address.=$obj->getName() . " ";

                        $address.=$obj->getStreet() . ", ";

                        if($obj->getComplement())
                            $address.=$obj->getComplement() . ", ";

                        $address.=$obj->getZipCode() . " " . $obj->getCity() . ", " . $obj->getCountry()->getName()  ;
                        return    $address;
                    },
                    'query_builder' => function (EntityRepository $er) use ($customer) {
                        return $er->createQueryBuilder('a')
                            ->leftJoin('LilWorksStoreBundle:Customer','c','WITH','c.id = a.customer')
                            ->where('c.id = :customer_id')
                            ->setParameter('customer_id',$customer->getId())
                            ;
                    },
                ));
            }
            $form->add('shippingAddressString');
            $form->add('billingAddressString');

        });


        $builder->add('orderType', EntityType::class, array(
                'disabled'=>$disabledOrderType,
                'class'    => 'LilWorksStoreBundle:OrderType' ,
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return   $obj->getName()   ;
                },
            ));

        $builder->add('customer', EntityType::class, array(
                'class'    => 'LilWorksStoreBundle:Customer' ,
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return   $obj->getFirstName() . " " . $obj->getLastName(). " " . $obj->getCompanyName()  ;
                },
            ));
        if($order->getOrderType()){

            $builder->add('ordersOrderSteps', CollectionType::class, array(
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => OrdersOrderStepsType::class
            ));
            if($order->getOrderType()->getTag() == "FACTURE"){
                $builder->add('ordersPaymentMethods', CollectionType::class, array(
                    'entry_options'  => array(
                        'context'  => $context,
                    ),
                    'constraints' => array(new Valid()),
                    'mapped'=>true,
                    'allow_add'=>true,
                    'required' => false,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'by_reference' => false,
                    'entry_type'   => OrdersPaymentMethodsType::class
                ));
            }
        }

        $builder->add('ordersProducts', CollectionType::class, array(
                'constraints' => array(new Valid()),
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => OrdersProductsType::class,
                'entry_options'  => array(
                    'orderId'  => $order->getId(),
                    'context'  => $context
                ),
            ));
        $builder->add('userComment',null,array(
                'label'=>'lilworks.storebundle.order.usercomment',
                'attr' => ['class' => 'text-editor'],
            ));
        $builder->add('storeComment',null,array(
                'label'=>'lilworks.storebundle.order.storecomment',
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
            'data_class' => 'LilWorks\StoreBundle\Entity\Order',
            'context'=>null,
            'cascade_validation' => true,
        ));
        $resolver->setRequired('orderUtils');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_order';
    }


}
