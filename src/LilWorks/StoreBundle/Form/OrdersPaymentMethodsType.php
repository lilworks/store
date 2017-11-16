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

class OrdersPaymentMethodsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $context = $options["context"];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($context) {
            $orderPaymentMethod = $event->getData();
            $form = $event->getForm();



            if( $orderPaymentMethod && $orderPaymentMethod->getPaymentMethod()->getTag() == "COUPON" ){
                $customer = $orderPaymentMethod->getOrder()->getCustomer();
                if($customer){
                    $form->add('coupon', EntityType::class, array(
                        'label'=>'storebundle.coupon',
                        'class'    => 'LilWorksStoreBundle:Coupon' ,
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => false ,
                        'multiple' => false,
                        'choice_label' => function ($obj) {
                            return $obj->getReference() . " " . $obj->getAmount();
                        },
                        'query_builder' => function (EntityRepository $er) use ($customer,$context) {
                            $now = new \DateTime();
                            $q = $er->createQueryBuilder('c')
                                ->where('c.customer = :customer_id')
                                #->andWhere('c.validity IS NULL OR c.validity >= :now')
                                ->setParameter('customer_id',$customer->getId())
                               # ->setParameter('now',$now->format('Y-m-d \O\n H:i:s'))
                            ;
                            if($context == "online"){
                                return $q->andWhere('c.availableOnline = 1');
                            }

                            return $q;

                        },
                    ));
                }
            }
        });

        $builder
            ->add('paymentMethod', EntityType::class, array(
                'label'=>'storebundle.paymentmethod',
                'class'    => 'LilWorksStoreBundle:PaymentMethod' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'required' => true ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false
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
            'data_class' => 'LilWorks\StoreBundle\Entity\OrdersPaymentMethods',
            'context'=>null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_ordersPaymentMethods';
    }


}
