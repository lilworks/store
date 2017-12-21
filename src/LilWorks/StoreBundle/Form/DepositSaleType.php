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
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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

            }

        });
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {

            $depositSale = $event->getData();
            $form = $event->getForm();

            if( $depositSale->getStatus() && $depositSale->getStatus()->getTag()=="DONE") {

                $form->add('depositSalesPaymentMethods', CollectionType::class, array(
                    'label' => 'storebundle.paymentmethods',
                    'entry_options' => array(
                        'context' => null
                    ),
                    'constraints' => array(new Valid()),
                    'mapped' => true,
                    'allow_add' => true,
                    'required' => false,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'by_reference' => false,
                    'entry_type' => DepositSalesPaymentMethodsType::class
                ));
            }
        });
            $builder
                ->add('status', EntityType::class, array(
                    'label'=>'storebundle.depositsale.status',
                    'class'    => 'LilWorksStoreBundle:DepositSaleStatus' ,
                    'required' => true ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => false,
                    'choice_label' => function ($obj) {
                        return   $obj->getName() ;
                    },
                    'attr' => array(
                        'class'=>'selectpicker',
                        'data-live-search'=>'true',
                        'data-actions-box'=>true,
                        'data-width'=>"300px"
                    )
                ))
            ->add('customer', EntityType::class, array(
                'label'=>'storebundle.customer',
                'class'    => 'LilWorksStoreBundle:Customer' ,
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return    $obj->getLastName(). " " . $obj->getFirstName() . " " . $obj->getCompanyName()  ;
                },
                'query_builder' => function (EntityRepository $er)  {
                    return  $er->createQueryBuilder('c')
                        ->orderBy('c.lastName', 'ASC')
                    ;

                },
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px"
                )
            ))
            ->add('product', EntityType::class, array(
                'label'=>'storebundle.product',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'required' => true ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {


                    return   $obj->getBrand()->getName() . " " . $obj->getName() . (!$obj->getDepositSale())?:" (".$obj->getDepositSale()->getReference().")" ;
                },
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('p')
                        ->leftJoin('p.brand','b')
                        ->leftJoin('p.depositSale','ds')
                        ->where('p.isSecondHand = 1')
                        ->andWhere('p.isArchived != 1')
                        #->andWhere('ds.product IS NULL')
                        ->orderBy('ds.reference','asc')
                        #->orderBy('b.name','asc')
                        #->addOrderBy('p.name','asc')
                        ;

                },
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px"
                )
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
            'data_class' => 'LilWorks\StoreBundle\Entity\DepositSale',
            'csrf_protection' => false,
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
