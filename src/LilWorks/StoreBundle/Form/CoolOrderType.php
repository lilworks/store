<?php

namespace LilWorks\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Tests\Extension\Core\Type\TextTypeTest;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Valid;

class CoolOrderType extends AbstractType
{
    private $orderManager;
    public function __construct($orderManager,$context,$mode){
        $this->orderManager = $orderManager;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('orderType', EntityType::class, array(
            'label'=>'storebundle.ordertype',
          # 'disabled'=>$disabledOrderType,
            'class'    => 'LilWorksStoreBundle:OrderType' ,
            'required' => false ,
            'mapped'=> true,
            'expanded' => false ,
            'multiple' => false,
            'choice_label' => function ($obj) {
                return   $obj->getName()   ;
            },
        ));
        $builder->add('reference',null,array(
            'label'=>'storebundle.reference',
            'required'=>false,
            'help'=>'storebundle.help.reference.leaveblankforauto',
        ));

        $builder->add('customer', EntityType::class, array(
            'label'=>'storebundle.customer',
            'class'    => 'LilWorksStoreBundle:Customer' ,

            'choice_label' => function ($obj) {
                return   $obj->getFirstName() . " " . $obj->getLastName(). " " . $obj->getCompanyName()  ;
            },
            'required' => false ,
            'mapped'=> true,
            'expanded' => false ,
            'multiple' => false,
            'attr' => array(
                'class'=>'selectpicker',
                'data-live-search'=>'true',
                'data-actions-box'=>true,
                'data-width'=>"300px"
            )
        ));
        $builder->add('ordersProducts', CollectionType::class, array(
            'label'=>'storebundle.products',
            'constraints' => array(new Valid()),
            'mapped'=>true,
            'allow_add'=>true,
            'required' => false,
            'allow_delete' => true,
            'delete_empty' => true,
            'by_reference' => false,
            'entry_type'   => OrdersProductsType::class,
            'entry_options'  => array(
                #'orderId'  => $order->getId(),
                #'context'  => $context
            ),



        ));

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
            'csrf_protection' => false,
        ));
        //$resolver->setRequired('orderUtils');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_order';
    }


}
