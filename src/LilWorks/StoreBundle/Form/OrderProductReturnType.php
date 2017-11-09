<?php

namespace LilWorks\StoreBundle\Form;



use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class OrderProductReturnType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $orderProductReturn = $builder->getData();
        $orderProduct = $orderProductReturn->getOrderProduct();

        $builder

            ->add('description',null, array(
                'label'    => 'storebundle.description' ,
            ))
            ->add('reference',null, array(
                'label'    => 'storebundle.reference' ,
                'required' => false,
                'help'=>'storebundle.help.reference.leaveblankforauto'
            ))
            ->add('isArchived',null, array(
                'label'    => 'storebundle.isarchived' ,
                'required' => false,
                'help'=>'storebundle.help.return.archived'
            ))
            ->add('returnedAt',null,array(
                'help'=>'storebundle.help.leaveblankfornow',
                'label'=>'storebundle.returnedat',
                'required'=>false,
                'attr' => ['class' => 'datepicker'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yy',
                'required'=>false
            ))
            ->add('quantity',null,array(
                'label'=>'storebundle.quantity',
                'data'=>$orderProduct->getQuantity()
            ))
            ->add('shippingMethod', EntityType::class, array(
                'label'=>'storebundle.return.shippingmethod',
                'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return   $obj->getName()   ;
                },
                'attr' => array(
                    'class'=>'selectpicker',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"300px"
                )
            ))
            ->add('returnsPaymentMethods', CollectionType::class, array(
                'label'=>'storebundle.paymentmethods',
                'constraints' => array(new Valid()),
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => ReturnsPaymentMethodsType::class
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\OrderProductReturn',
        ));
    }
}
