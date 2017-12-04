<?php

namespace SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CombinationRealShippingMethodsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $combinations = $options['combinations'];
        var_dump(count($combinations));


        foreach($combinations as $combination){

        }


          $builder
              ->add('combination', ChoiceType::class, array(
                  'label'=>'sitebundle.shippingmethods',
                  'mapped'=>false,
                  'required'=>true,
                  'expanded'=>true,
                  #'choices'  =>$choices,
                  'choices'  =>null,
                  #'data'=>1
              ));

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          #  'data_class' => 'LilWorks\StoreBundle\Entity\BasketsRealShippingMethods'
            'combinations'=>null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lilworks_storebundle_shippingmethod';
    }

}
