<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
/*            ->add('username',null,array(
                'label'=>'appbundle.username'
            ))*/
            ->add('password',null,array(
                'label'=>'appbundle.password'
            ))
            ->add('email',null,array(
                'label'=>'appbundle.email'
            ))
            ->add('enabled',ChoiceType::class,array(
                'label'=>'appbundle.user.enabled',
                'expanded'=>true,
                'choices' => array(
                    'storebundle.no' => 0,
                    'storebundle.yes' => 1,
                ),
            ))
            ->add('customer', EntityType::class, array(
                'label'=>'appbundle.customer',
                'class'    => 'LilWorksStoreBundle:Customer' ,
                'choice_label' => function ($obj) { return
                    $obj->getFirstName() ." ".$obj->getLastName() ." " . $obj->getCompanyName() ;
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.firstName','asc')
                        ;
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
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
