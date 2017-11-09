<?php
namespace SiteBundle\Form;



use LilWorks\StoreBundle\Entity\BasketsRealShippingMethods;
use LilWorks\StoreBundle\Entity\ShippingMethod;
use SiteBundle\Form\EventListener\AddShippingMethodListener;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Valid;
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

            if(count($basket->getBasketsProducts())>0){
                $form->add('empty', SubmitType::class, array(
                    'label'=>'sitebundle.button.empty',
                    'attr' => array('class' => 'btn btn-warning'),
                ));
                $form
                    ->add('basketsProducts', CollectionType::class, array(
                        'label'=>'sitebundle.products',
                        'mapped'=>true,
                        'allow_add'=>false,
                        'required' => false,
                        'allow_delete' => true,
                        'delete_empty' => true,
                        'by_reference' => false,
                        'constraints' => array(new Valid()),
                        'entry_type'   => BasketProductType::class
                    ))
                ;



            }else{
                $form->add('empty', ButtonType::class, array(
                     'disabled'=>true,
                    'label'=>'sitebundle.button.empty',
                    'attr' => array('class' => 'btn btn-warning'),
                ));
            }

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
                        'label'=>'sitebundle.shippingaddress',
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
                    'label'=>'sitebundle.billingaddress',
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
                            'label'=>'sitebundle.shippingmethods',
                            'mapped'=>false,
                            'expanded'=>true,
                            'choices'  =>$choices,
                            'data'=>$data
                        ));

                    }
                $selectedBillingAddress = $basket->getBillingAddress() ;
                $selectedShippingMethods = $basket->getBasketsRealShippingMethods() ;


                if( (count($selectedShippingMethods)>0 || $selectedBillingAddress) && count($basket->getBasketsProducts())>0) {
                    $form->add('order', SubmitType::class, array(
                        'label'=>'sitebundle.order',
                        'attr' => array('class' => 'btn btn-danger'),
                    ));
                }else{
                    $form->add('order', SubmitType::class, array(
                        'disabled'=>true,
                        'label'=>'sitebundle.order',
                        'attr' => array('class' => 'btn btn-danger'),
                    ));
                }

            }
        });


        $builder->add('update', SubmitType::class, array(
            'label'=>'sitebundle.button.update',
            'attr' => array('class' => 'btn btn-success'),
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