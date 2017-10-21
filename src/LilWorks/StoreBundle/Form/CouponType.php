<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CouponType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $coupon = $builder->getData();


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)  {
            $coupon = $event->getData();
            $form = $event->getForm();



            if($coupon->getCustomer()){

                /*
                foreach($coupon->getCustomer()->getOrders()->getOrdersPaymentMethods() as $paymentMethod){
                    if($paymentMethod->getCoupon() == $coupon){
                        $form->get('customer')->set
                    }
                }
                */


                $form->add('address', EntityType::class, array(
                    'label'=>'lilworks.storebundle.address',
                    'class'    => 'LilWorksStoreBundle:Address' ,
                    'required' => true ,
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
                    'query_builder' => function (EntityRepository $er) use ($coupon) {
                        return $er->createQueryBuilder('a')
                            ->leftJoin('LilWorksStoreBundle:Customer','c','WITH','c.id = a.customer')
                            ->where('c.id = :customer_id')
                            ->setParameter('customer_id',$coupon->getCustomer()->getId())
                            ;
                    },
                ));
            }

        });

        $builder
            ->add('customer', EntityType::class, array(
                'disabled'=>(count($coupon->getOrdersPaymentMethods())>0)?1:0,
                'label'=>'lilworks.storebundle.customer',
                'class'    => 'LilWorksStoreBundle:Customer' ,
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return   $obj->getFirstName() . " " . $obj->getLastName(). " " . $obj->getCompanyName()  ;
                },
            ))
            #->add('reference')
            ->add('amount',MoneyType::class,array(
                'label'=>'lilworks.storebundle.coupon.initamount',
            ))
            ->add('splitable',null,array(
                'label'=>'lilworks.storebundle.coupon.splitable',
            ))
            ->add('availableOnline',null,array(
                'label'=>'lilworks.storebundle.coupon.availableonline',
            ))
            ->add('validity',null,array(
                'label'=>'lilworks.storebundle.coupon.validity',
                'attr' => ['class' => 'datepicker'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yy',
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
            'data_class' => 'LilWorks\StoreBundle\Entity\Coupon'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_coupon';
    }


}
