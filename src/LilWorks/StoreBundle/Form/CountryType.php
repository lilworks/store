<?php

namespace LilWorks\StoreBundle\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class CountryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array(
                'label'=>'storebundle.name'
            ))
            ->add('tag',null,array(
                'label'=>'storebundle.tag'
            ))
            ->add('flag',null,array(
                'label'=>'storebundle.country.flag'
            ))
            ->add('isPublished',ChoiceType::class,array(
                'label'=>'storebundle.ispublished',
                'choices' => array(
                    'storebundle.no' => 0,
                    'storebundle.yes' => 1,
                ),
            ))
            ->add('shippingmethodsCountries', CollectionType::class, array(
                'label'=>'storebundle.country.alowedshippingmethods',
                'mapped'=>true,
                'allow_add'=>true,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => ShippingMethodsCountriesType::class
            ))

        ;

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Country'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_country';
    }


}
