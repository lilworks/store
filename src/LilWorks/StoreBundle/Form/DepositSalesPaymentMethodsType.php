<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Valid;

class DepositSalesPaymentMethodsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $context = $options["context"];
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($context) {
            $depositSalePaymentMethod = $event->getData();
            $form = $event->getForm();

        });

/*
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($context) {
            $depositSalePaymentMethod = $event->getData();
            $form = $event->getForm();

           // if(!is_null($depositSalePaymentMethod) && $depositSalePaymentMethod->getDepositSale()->getCustomer()){

                $customer = $depositSalePaymentMethod->getDepositSale()->getCustomer();

                $form->add('paymentMethod', EntityType::class, array(
                    'label'=>'storebundle.paymentmethod',
                    'class'    => 'LilWorksStoreBundle:PaymentMethod' ,
                    'choice_label' => function ($obj) { return   $obj->getName() ; },
                    'required' => true ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'choice_label' => function ($obj) {
                        return    $obj->getName();
                    },
                    'query_builder' => function (EntityRepository $er)  use ($customer) {

                        if($customer->getCoupons()){
                            return  $er->createQueryBuilder('pm');
                        }else{
                            return  $er->createQueryBuilder('pm')
                                ->where('pm.tag != :coupon')
                                ->setParameter(':coupon', 'COUPON')
                                ;
                        }
                    },
                ));
         //   }



        });
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($context) {

            $depositSalePaymentMethod = $event->getData();
            $form = $event->getForm();






            if(!is_null($depositSalePaymentMethod) && $depositSalePaymentMethod->getPaymentMethod()->getTag() == "COUPON") {
                $customer = $depositSalePaymentMethod->getDepositSale()->getCustomer();
                $form->add('coupon', EntityType::class, array(
                    'label'=>'storebundle.coupon',
                    'class'    => 'LilWorksStoreBundle:Coupon' ,
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'choice_label' => function ($obj) {
                        return $obj->getReference() . " (" . $obj->getAmount() ."€)";
                    },
                    'query_builder' => function (EntityRepository $er) use ($customer) {
                        $now = new \DateTime();
                        $q = $er->createQueryBuilder('c')
                            ->where('c.customer = :customer_id')
                            #->andWhere('c.validity IS NULL OR c.validity >= :now')
                            ->setParameter('customer_id',$customer->getId())
                            # ->setParameter('now',$now->format('Y-m-d \O\n H:i:s'))
                        ;

                        return $q;

                    },
                ));
            }
        });
*/


        $builder
            ->add('paymentMethod', EntityType::class, array(
                'label'=>'storebundle.paymentmethod',
                'class'    => 'LilWorksStoreBundle:PaymentMethod' ,
                'constraints' => array(new Valid()),
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'required' => true ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return    $obj->getName();
                },
                'query_builder' => function (EntityRepository $er)  {
                        return  $er->createQueryBuilder('pm');
                },
            ))
            ->add('payedAt',null,array(
                'label'=>'storebundle.paymentmethod.payedat',
                'attr' => ['class' => 'datepicker'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yy',
                'required'=>false
            ))
            ->add('amount',MoneyType::class,array(
                'label'=>'storebundle.paymentmethod.amount',
            ))
            ->add('description',null,array(
                'label'=>'storebundle.description',
                'required'=>false
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\DepositSalesPaymentMethods',
            'context'=>null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_depositSalesPaymentMethodsType';
    }


}
