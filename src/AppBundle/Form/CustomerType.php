<?php
namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;


class CustomerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('lastName',null,array(
                'required' => true,
                'label'=>'sitebundle.lastname'
            ))
            ->add('firstName',null,array(
                'label'=>'sitebundle.firstname',
                'required' => true
            ))

            ->add('companyName',null,array(
                'required' => false,
                'label'=>'sitebundle.companyname'

            ))

            ->add('phonenumbers', CollectionType::class, array(
                'label'=>'sitebundle.phonenumbers',
                'mapped'=>true,
                'allow_add'=>true,
                'required' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => PhoneNumberType::class
            ))


            ->add('addresses', CollectionType::class, array(
                'label'=>'sitebundle.addresses',
                'mapped'=>true,
                'allow_add'=>true,
                'required' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => AddressType::class
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           # 'validation_groups' => array('registration'),
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
