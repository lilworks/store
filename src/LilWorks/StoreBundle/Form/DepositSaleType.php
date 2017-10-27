<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class DepositSaleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)  {
            $depositSale = $event->getData();
            $form = $event->getForm();


            if($depositSale->getCustomer()){
                $form->add('address', EntityType::class, array(
                    'label'=>'storebundle.address',
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
                    'query_builder' => function (EntityRepository $er) use ($depositSale) {
                        return $er->createQueryBuilder('a')
                            ->leftJoin('LilWorksStoreBundle:Customer','c','WITH','c.id = a.customer')
                            ->where('c.id = :customer_id')
                            ->setParameter('customer_id',$depositSale->getCustomer()->getId())
                            ;
                    },
                ));
                $form->add('coupon', EntityType::class, array(
                    'label'=>'storebundle.depositsale.coupon',
                    'class'    => 'LilWorksStoreBundle:Coupon' ,
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'choice_label' => function ($obj) {
                        return    $obj->getReference();
                    },
                    'query_builder' => function (EntityRepository $er) use ($depositSale) {
                        $q = $er->createQueryBuilder('co')
                            ->leftJoin('LilWorksStoreBundle:Customer','cu','WITH','cu.id = co.customer')
                            ->leftJoin('LilWorksStoreBundle:DepositSale','ds','WITH','cu.id = ds.customer AND ds.coupon = co.id')
                            ->where('cu.id = :customer_id and ds.coupon IS NULL ')
                            ->setParameter('customer_id',$depositSale->getCustomer()->getId())
                            ;

                        if($depositSale->getCoupon()){
                            $q->orWhere('co.id = :coupon_id')
                                ->setParameter('coupon_id',$depositSale->getCoupon());
                        }
                        return $q;
                    },
                ));
            }

        });

            $builder
                ->add('status', EntityType::class, array(
                    'label'=>'storebundle.depositsale.status',
                    'class'    => 'LilWorksStoreBundle:DepositSaleStatus' ,
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'choice_label' => function ($obj) {
                        return   $obj->getName() ;
                    },
                ))
            ->add('customer', EntityType::class, array(
                'label'=>'storebundle.customer',
                'class'    => 'LilWorksStoreBundle:Customer' ,
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return   $obj->getFirstName() . " " . $obj->getLastName(). " " . $obj->getCompanyName()  ;
                },
            ))
            ->add('product', EntityType::class, array(
                'label'=>'storebundle.product',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return   $obj->getBrand()->getName() . " " . $obj->getName() ;
                },
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('p')
                        ->leftJoin('p.brand','b')
                        ->leftJoin('LilWorksStoreBundle:DepositSale','ds','WITH','ds.product = p.id')
                        ->where('p.isSecondHand = 1')
                        ->andWhere('p.isArchived != 1')
                        ->andWhere('ds.product IS NULL')
                        ->orderBy('b.name','asc')
                        ->addOrderBy('p.name','asc')
                        ;

                },
            ))

            ->add('priceBuying',MoneyType::class,array(
                'label'=>'storebundle.depositsale.pricebuying',
            ))
            ->add('priceSelling',MoneyType::class,array(
                'label'=>'storebundle.depositsale.priceselling',
            ))

            ->add('deposedAt',null,array(
                'attr' => ['class' => 'datepicker'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yy',
                'label'=>'storebundle.depositsale.deposedat',
            ))
            ->add('selledAt',null,array(
                'label'=>'storebundle.depositsale.selledat',
                'attr' => ['class' => 'datepicker'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yy',
            ))
            ->add('description',null,array(
                'label'=>'storebundle.description',
                'attr' => ['class' => 'editor-text'],
            ))
            ->add('descriptionInternal',null,array(
                 'label'=>'storebundle.descriptioninternal',
                 'attr' => ['class' => 'editor-text'],
            ))

            ;


    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\DepositSale'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_depositSale';
    }


}
