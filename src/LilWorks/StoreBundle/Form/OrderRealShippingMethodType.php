<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
class OrderRealShippingMethodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'];
        $shippingAddress = $options['shippingAddress'];

        $builder
            ->add('shippingMethod', EntityType::class, array(
                'label'=>'lilworks.storebundle.shippingmethod',
                'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
                'choice_label' => function ($obj) { return   $obj->getName() ; },
                'query_builder' => function (EntityRepository $er) use ($shippingAddress,$context){
                    if(
                        $shippingAddress &&
                        $context == "online"
                    ){
                        return $er->createQueryBuilder('sm')
                            ->leftJoin('LilWorksStoreBundle:ShippingMethodsCountries','smc','WITH','smc.shippingMethod=sm.id')
                            ->where('smc.country = :country_id')
                            ->setParameter('country_id',$shippingAddress->getCountry()->getId())
                            ;
                    }
                    return $er->createQueryBuilder('sm');
                },
                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => false
            ))

            ->add('shippedAt',null,array(
                'label'=>'lilworks.storebundle.shippingmethod.shippedat',
                'attr' => ['class' => 'datepicker'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yy',
                'required'=>false
            ))
            ->add('receivedAt',null,array(
                'label'=>'lilworks.storebundle.shippingmethod.receivedat',
                'attr' => ['class' => 'datepicker'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yy',
                'required'=>false
            ))
            ->add('reference',null,array(
                'label'=>'lilworks.storebundle.reference',
            ))
            ->add('price',MoneyType::class,array(
                'label'=>'lilworks.storebundle.shippingmethod.price',
            ))
            ->add('description',null,array(
                'label'=>'lilworks.storebundle.description',
            ))


        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\OrdersRealShippingMethods',
            'context'=>null,
            'shippingAddress'=>null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_ordersRealShippingmethods';
    }


}
