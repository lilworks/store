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
        (is_object($user))?
            $customer = $user->getCustomer():
            $customer = null;

/*
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($customer) {
            $basket = $event->getData();
            $form = $event->getForm();
            $form->add('basketsRealShippingMethods', ChoiceType::class, array(
                'label'=>'sitebundle.shippingmethods',
                'mapped'=>false,
                'required'=>true,
                'expanded'=>true,
                //'choice_value' => 'id', // this explicit line solves the problem
                'choices'  =>  $this->getShippingMethodsCombinationChoices($this->basketService->shippingMethods()),
            ));
        });
*/
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

            if ($customer) {
                $form->add('shippingMethod', SubmitType::class, array(
                    'label'=>'sitebundle.shippingmethod',
                    'attr' => array('class' => 'btn btn-primary'),
                ));

            }else{
                $form->add('shippingMethod', SubmitType::class, array(
                    'label'=>'sitebundle.shippingmethod',
                    'attr' => array('class' => 'btn btn-primary','disabled'=>'disabled')
                ));

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