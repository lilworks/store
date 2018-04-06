<?php

namespace LilWorks\StoreBundle\Form;



use LilWorks\StoreBundle\Entity\OrdersProducts;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class OrdersProductsType extends AbstractType
{

    public $context;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->context = $options["context"];

                $builder
                    ->addEventListener(
                        FormEvents::PRE_SET_DATA,
                        array($this, 'onPreSetData')
                    )
                    ->addEventListener(
                        FormEvents::SUBMIT,
                        array($this, 'onSubmitData')
                    )

                ;

    }


    public function onSubmitData(FormEvent $event)
    {
        $orderProduct = $event->getData();
        $form = $event->getForm();

        if($form->has('new')){

            $orderProduct->setName($orderProduct->getProduct()->getBrand()->getName() . " " . $orderProduct->getProduct()->getName());
            $orderProduct->setQuantity(1);
            $orderProduct->setIsSecondHand($orderProduct->getProduct()->getIsSecondHand());
            if($this->context == 'online'){
                $orderProduct->setPrice($orderProduct->getProduct()->getPriceOnline());
                foreach($orderProduct->getProduct()->getWarrantiesOnline() as $warranty){
                    $orderProduct->addWarranty($warranty);
                }
            }else{
                $orderProduct->setPrice($orderProduct->getProduct()->getPriceOffline());
                foreach($orderProduct->getProduct()->getWarrantiesOffline() as $warranty){
                    $orderProduct->addWarranty($warranty);
                }
                foreach($orderProduct->getProduct()->getTaxesOffline() as $tax){
                    $orderProduct->addTax($tax);
                }
            }




        }
    }
    public function onPreSetData(FormEvent $event)
    {
        $orderProduct = $event->getData();
        $form = $event->getForm();


        $context = $this->context;


        if($orderProduct instanceof OrdersProducts){
            // product selected
            $form
                ->add('name',null,array(
                    'label'=>'storebundle.name',
                    'required'=>true,
                ))
                ->add('quantity',null,array(
                    'label'=>'storebundle.quantity',
                    'required'=>true,
                ))
                ->add('price',MoneyType::class,array(
                    'label'=>'storebundle.price',
                    'required'=>true,
                ))
                ->add('isSecondHand',ChoiceType::class,array(
                    'label'=>'storebundle.issecondhand',
                    'choices' => array(
                        'storebundle.no' => 0,
                        'storebundle.yes' => 1,
                    ),

                ));

            $form
                ->add('taxes', EntityType::class, array(
                    'label'=>'storebundle.taxes',
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
                    },
                    'data'=>$orderProduct->getTaxes(),
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => true ,
                    'choices'=>$this->getTaxChoices(),
                    'data'=>$orderProduct->getTaxes()
                ))
                ->add('warranties', EntityType::class, array(
                    'label'=>'storebundle.warranties',
                    'class'    => 'LilWorksStoreBundle:Warranty' ,
                    'choice_label' => function ($obj) { return   $obj->getName() ; },
                    'query_builder' => function (EntityRepository $er) use ($orderProduct){
                        return $er->createQueryBuilder('w')
                            #->innerJoin('t.productsOffline', 'p')
                            #->where('p.id = :product_id')
                            #->setParameter('product_id',$orderProduct->getProduct()->getId())
                            ;
                    },
                    'choices'=>$this->getWarrantyChoices(),
                    'data'=>$orderProduct->getWarranties(),
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => false ,
                    'multiple' => true
                ))
                ->add('orderRealShippingMethod', EntityType::class, array(
                    'label'=>'storebundle.shippingmethod',
                    'class'    => 'LilWorksStoreBundle:OrdersRealShippingMethods' ,
                    'choice_label' => function ($obj) {
                        return   $obj->getShippingMethod()->getName() . " " . $obj->getReference() ;
                    },
                    'query_builder' => function (EntityRepository $er) use ($orderProduct){
                        return $er->createQueryBuilder('orsm')
                            ->where('orsm.order = :id')
                            ->setParameter('id',$orderProduct->getOrder()->getId())
                            ;
                    },
                    'required' => false ,
                    'mapped'=> true,
                    'expanded' => true ,
                    'multiple' => false
                ))
                ->add('serialNumber',null,array(
                    'label'=>'storebundle.product.serialnumber',
                    'required'=>false
                ))
                ->add('description',null,array(
                    'label'=>'storebundle.description',
                    'required'=>false
                ))
                ;

            ;
        }else{
            // product not selected
            $form->add('product', EntityType::class, array(
                'label'=>'storebundle.product',
                'class'    => 'LilWorksStoreBundle:Product' ,
                'choice_label' => function ($obj) { return   $obj->getBrand()->getName() . " " . $obj->getName() . " (" . $obj->getStock() .")" ; },
                'query_builder' => function (EntityRepository $er) use ($context){
                    $q = $er->createQueryBuilder('p')
                        #->select('p.id')
                        ->leftJoin('LilWorksStoreBundle:Brand','b','WITH','b.id = p.brand')
                        ->where('p.isArchived != 1')
                        ->orderBy('b.name , p.name', 'ASC');
                    if($context == "online")
                        $q->where("p.isPublished = 1")
                            ->andWhere('p.isArchived != 1');

                    //$q->setMaxResults(10);


                    return $q;
                },
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px",
                )

            ))
                ->add('new',HiddenType::class,array('mapped'=>false))
            ;
        }


    }

    private function getWarrantyChoices()
    {
        $warranties = $this->entityManager->getRepository('LilWorksStoreBundle:Warranty')->findAll();
        return $warranties;
    }

    private function getTaxChoices()
    {
        $taxes = $this->entityManager->getRepository('LilWorksStoreBundle:Tax')->findAll();
        return $taxes;
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
            //'cascade_validation' => true
        ));

    }
}
