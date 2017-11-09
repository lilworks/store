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

class ReturnsPaymentMethodsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $returnPaymentMethod = $event->getData();
            $form = $event->getForm();

            if($returnPaymentMethod && $returnPaymentMethod->getPaymentMethod()->getTag() == "COUPON"){
                $form->add('coupon', EntityType::class, array(
                    'help'=>'storebundle.help.return.couponwillbecreated',
                    'label'=>'storebundle.coupon',
                    'class'    => 'LilWorksStoreBundle:Coupon' ,
                    'choice_label' => function ($obj) { return   $obj->getReference() ; },
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false
                ));
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
                'help'=>'storebundle.help.leaveblankfornow',
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
            'data_class' => 'LilWorks\StoreBundle\Entity\ReturnsPaymentMethods',
            'context'=>null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_returnsPaymentMethods';
    }


}
