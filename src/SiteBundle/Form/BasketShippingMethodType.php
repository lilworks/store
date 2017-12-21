<?php
namespace SiteBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

class BasketShippingMethodType extends AbstractType
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
        (is_object($user))?
            $customer = $user->getCustomer():
            $customer = null;


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($customer) {
            $basket = $event->getData();
            $form = $event->getForm();


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
                    },

                    'query_builder' => function (EntityRepository $er) use ($customer) {
                        return $er->createQueryBuilder('a')
                            ->leftJoin('LilWorksStoreBundle:Customer', 'c', 'WITH', 'c.id = a.customer')
                            ->where('c.id = :id')
                            ->setParameter('id',$customer->getId())
                            ->orderBy('a.id','desc')
                            ;
                    },

                    'required' => true ,
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
                            ->orderBy('a.id','desc')
                            ;
                    },
                    'required' => true ,
                    'mapped'=> true,
                    'expanded' => true ,
                    'multiple' => false
                ));
                $form->add('basketsRealShippingMethods', ChoiceType::class, array(
                    'label'=>'sitebundle.shippingmethods',
                    'mapped'=>false,
                    'required'=>true,
                    'expanded'=>true,
                    'choices'  =>  $this->getShippingMethodsCombinationChoices($this->basketService->shippingMethods()),
                ));


                if($this->basketService->isAllShipped()){
                    $form->add('order', SubmitType::class, array(
                        'label'=>'sitebundle.order',
                        'attr' => array('class' => 'btn btn-warning'),
                    ));
                }

            }


        });




        $builder->add('update', SubmitType::class, array(
            'label'=>'sitebundle.button.update',
            'attr' => array('class' => 'btn btn-success'),
        ));


    }
    private function getShippingMethodsCombinationChoices($datas){
        // datas is an array of combination
        $choices = array();

        foreach($datas as $combination){
            $first = null;
            $text = count($combination["combData"]["datas"]) . ' colis';

            foreach($combination["combData"]["datas"] as $combData){

                (!$first)?
                    $first=true && $text.= " " . $combData["shippingMethod"]->getName() :
                    $text.= " + " . $combData["shippingMethod"]->getName();
            }
            $text.= " " . $combination["price"] . "€";
            $choices[$text] = $combination["combId"];
        }
        return $choices;
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