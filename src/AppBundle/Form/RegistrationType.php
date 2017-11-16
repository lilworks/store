<?php
namespace AppBundle\Form;

use AppBundle\Entity\User;
use LilWorks\StoreBundle\Entity\Address;
use LilWorks\StoreBundle\Entity\Customer;
use LilWorks\StoreBundle\Entity\PhoneNumber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add('customer', CustomerType::class)
            ->remove('username');



        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                $customer = new Customer();
                $customer->addPhonenumber(new PhoneNumber());
                $customer->addAddress(new Address());


                if(is_null($user)){
                    $user = new User();
                }
                $user->setCustomer($customer);
                $event->setData($user);
        });
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
        });
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'validation_groups' => array('registration'),
        ));
    }
}