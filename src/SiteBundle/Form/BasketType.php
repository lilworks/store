<?php
namespace SiteBundle\Form;



use LilWorks\StoreBundle\Entity\BasketsRealShippingMethods;
use LilWorks\StoreBundle\Entity\ShippingMethod;
use SiteBundle\Form\EventListener\AddShippingMethodListener;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use SiteBundle\Service\Basket as BasketService;

class BasketType extends AbstractType
{


    private $token_storage;
    private $basketService;
    private $em;

    public function __construct(TokenStorage $token_storage,BasketService $basketService,\Doctrine\ORM\EntityManager $em)
    {
        $this->token_storage = $token_storage;
        $this->basketService = $basketService;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $this->token_storage->getToken()->getUser();
        if(is_object($user)){
            $customer = $user->getCustomer();
        }else{
            $customer = null;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($customer) {
            $basket = $event->getData();
            $form = $event->getForm();
            $totals = $this->basketService->getTotals();
            $shippingMethods = $this->basketService->shippingMethods();

            $comb = array();
            foreach($shippingMethods as $k=>$shippingMethod){
                if(!isset($comb[$k])){
                    $comb[$k] = array();
                    $basketRealShippingMethod = new BasketsRealShippingMethods();
                }
                foreach($shippingMethod["datas"] as $ksm=>$v){
                    array_push($comb[$k],$ksm);
                }

            }



            if ($customer) {

                $form->add('shippingAddress', EntityType::class, array(
                        'class'    => 'LilWorksStoreBundle:Address' ,
                        'choice_label' => function ($obj) {
                            $address = "";
                            if($obj->getName())
                                $address.=$obj->getName() . " ";

                            $address.=$obj->getStreet() . ", ";

                            if($obj->getComplement())
                                $address.=$obj->getComplement() . ", ";

                                $address.=$obj->getZipCode() . " " . $obj->getCity() . ", " . $obj->getCountry()->getName()  ;
                             return    $address;
                            // return    "lilworks.translations.test"  ;
                        },

                        'query_builder' => function (EntityRepository $er) use ($customer) {
                            return $er->createQueryBuilder('a')
                                ->leftJoin('LilWorksStoreBundle:Customer', 'c', 'WITH', 'c.id = a.customer')
                                ->where('c.id = :id')
                                ->setParameter('id',$customer->getId())
                                ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => true ,
                        'multiple' => false,
                        'choice_translation_domain' => true,
                    ));

                $form->add('billingAddress', EntityType::class, array(
                        'class'    => 'LilWorksStoreBundle:Address' ,
                        'choice_label' => function ($obj) {
                            $address = "";
                            if($obj->getName())
                                $address.=$obj->getName() . " ";

                            $address.=$obj->getStreet() . ", ";

                            if($obj->getComplement())
                                $address.=$obj->getComplement() . ", ";

                            $address.= $obj->getZipCode() . " " . $obj->getCity() . ", " . $obj->getCountry()->getName()  ;
                            return    $address;
                        },
                        'query_builder' => function (EntityRepository $er)  use ($customer) {
                            return $er->createQueryBuilder('a')
                                ->leftJoin('LilWorksStoreBundle:Customer', 'c', 'WITH', 'c.id = a.customer')
                                ->where('c.id = :id')
                                ->setParameter('id',$customer->getId())
                                ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => true ,
                        'multiple' => false
                    ));
                    $selectedShippingAddress = $basket->getShippingAddress() ;

                    if($selectedShippingAddress){
/*
                        $form->add('realShippingMethods', EntityType::class, array(
                            'class'    => 'LilWorksStoreBundle:RealShippingMethod' ,
                            'choice_label' => function ($obj) use ($totals,$selectedShippingAddress) {
                                if($totals["TTC"]>= $obj->getFreeTriggerByCountry($selectedShippingAddress->getCountry()->getId())){
                                    return    $obj->getName() . " 0 €"  ;
                                }else{
                                    return    $obj->getName() . " " . $obj->getPriceByCountry($selectedShippingAddress->getCountry()->getId())."€ "  ;
                                }

                            },
                            'query_builder' => function (EntityRepository $er) use ($arraySm) {
                                return $er->createQueryBuilder('sm')
                                    ->where('sm.id IN (:ids)')
                                    ->setParameter('ids',$arraySm)
                                    ;
                            },
                            'required' => false ,
                            'mapped'=> true,
                            'expanded' => true ,
                            'multiple' => false

                        ));
*/
                        $choices = array();
                        $choices["retrait magasin"] = null;

                        foreach($comb as $k=>$groupOfSm){
                            $string = $shippingMethods[$k]['price'] . "€ (".count($groupOfSm) ." colis )";
                            foreach($groupOfSm as $sm){
                                $sm = $this->em->getRepository('LilWorksStoreBundle:ShippingMethod')->find($sm);
                                $string.=$sm->getName() . " ";

                            }
                            $choices[$string] = $k;
                        }

                        $data = null;
                        if(null!==$basket->getBasketsRealShippingMethods()){
                            $currentSms  = array();
                            foreach($basket->getBasketsRealShippingMethods() as $currentSm){
                                array_push($currentSms,$currentSm->getShippingMethod()->getId());
                            }
                            sort($currentSms);
                            foreach($comb as $k=>$groupOfSm){
                                sort($groupOfSm);
                                if($currentSms==$groupOfSm){
                                    $data = $k;
                                }
                            }

                        }
                        $form->add('basketsRealShippingMethods', ChoiceType::class, array(
                            'mapped'=>false,
                            'expanded'=>true,
                            'choices'  =>$choices,
                            'data'=>$data
                        ));


                        /*
                        $form->add('basketsRealShippingMethods', CollectionType::class, array(
                            'mapped'=>true,
                            'allow_add'=>false,
                            'required' => false,
                            'allow_delete' => true,
                            'delete_empty' => true,
                            'by_reference' => false,
                            'entry_type'   => RealShippingMethodType::class
                        ));*/
                    }
                $selectedBillingAddress = $basket->getBillingAddress() ;
                $selectedShippingMethods = $basket->getBasketsRealShippingMethods() ;
                /*
                if($selectedShippingMethods || $selectedBillingAddress ){
                    $form->add('paymentMethod', EntityType::class, array(
                        'class'    => 'LilWorksStoreBundle:PaymentMethod' ,
                        'choice_label' => function ($obj) use ($totals) {
                            $t = $totals["TTC"]+$totals["SM"];
                            return    $obj->getName() . " ". $t ."€"  ;
                        },
                        'query_builder' => function (EntityRepository $er)  {
                            return $er->createQueryBuilder('pm')
                                ->where('pm.isPublished = :isPublished')
                                ->setParameter('isPublished',1)
                                ;
                        },
                        'required' => false ,
                        'mapped'=> true,
                        'expanded' => true ,
                        'multiple' => false
                    ));
                }
                */
                $selectedPaymentMethod = $basket->getPaymentMethod() ;

                if( count($selectedShippingMethods)>0 || $selectedBillingAddress) {
                    $form->add('order', SubmitType::class, array(
                        'attr' => array('class' => 'btn btn-success'),
                    ));
                }

            }
        });



        $builder
            //->addEventSubscriber(new AddShippingMethodListener())
            ->add('basketsProducts', CollectionType::class, array(
                'mapped'=>true,
                'allow_add'=>false,
                'required' => false,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'entry_type'   => BasketProductType::class
            ))
        ;

        $builder->add('update', SubmitType::class, array(
            'attr' => array('class' => 'btn'),
        ));
        $builder->add('empty', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-warning'),
        ));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\Basket'
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sitebundle_basket';
    }

}