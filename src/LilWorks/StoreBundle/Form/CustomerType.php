<?php

namespace LilWorks\StoreBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;

class CustomerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',null,array(
                'label'=>'lilworks.storebundle.firstname'
            ))
            ->add('lastName',null,array(
                'label'=>'lilworks.storebundle.lastname'
            ))
            ->add('companyName',null,array(
                'label'=>'lilworks.storebundle.companyname'
            ))
            ->add('remoteUser',null,array(
                'label'=>'lilworks.storebundle.remoteuser'
            ))


            ->add('phonenumbers', CollectionType::class, array(
                'label'=>'lilworks.storebundle.phonenumbers',
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => PhoneNumberType::class
            ))


            ->add('user', EntityType::class, array(
                'label'=>'lilworks.storebundle.user',
                'class'    => 'AppBundle:User' ,
                'choice_label' => function ($obj) { return   $obj->getEmail() ." | " .  $obj->getUsername()  ; },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.email', 'ASC');
                },
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false
            ))
            ->add('addresses', CollectionType::class, array(
                'label'=>'lilworks.storebundle.addresses',
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => AddressType::class
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Customer'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_customer';
    }


}
