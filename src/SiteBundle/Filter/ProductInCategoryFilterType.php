<?php
namespace SiteBundle\Filter;

use AppBundle\Entity\Brand;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\Query\Expr;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductInCategoryFilterType extends AbstractType
{

    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack,\Doctrine\ORM\EntityManager $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $category = $options['data']["category"];
        $rbrands = $this->em->createQueryBuilder()
            ->select("b.id as id ,COUNT(p) as countp")

            ->from("LilWorksStoreBundle:Brand","b")
            ->where("b.isPublished = 1")
            ->join("b.products","p")
            ->where("p.isPublished = 1")
            ->andWhere('p.priceOnline IS NOT NULL ')
            ->andWhere(':category_id MEMBER OF p.categories')

            ->having('COUNT(p)>0')
            ->groupBy('b.id')
            ->orderBy('p.priceOnline', 'asc')
            ->setParameter('category_id',$category->getId())
            ->getQuery()
            ->getArrayResult()
        ;
        $brands = array();
        $productsInBrand = array();
        foreach($rbrands as $brand){
            array_push($brands,$brand["id"]);
            $productsInBrand[$brand["id"]]=$brand["countp"];
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($brands,$productsInBrand) {
            $eventData = $event->getData();
            $form = $event->getForm();

            $form->add('brand', Filters\EntityFilterType::class, array(
                'empty_data'=>$brands,
                'label'    =>  'sitebundle.brand',
                'class'    =>  'LilWorksStoreBundle:Brand',
                'choice_label' => function ( $brand ) use ($productsInBrand){
                    return $brand->getName() . "(".$productsInBrand[$brand->getId()].")" ;
                },
                'query_builder' => function (EntityRepository $er) use ($brands) {
                    return $er->createQueryBuilder('b')
                        ->where('b.id IN (:ids)')
                        ->andWhere('b.isPublished = 1')
                        ->setParameter('ids',$brands)
                        ;
                },
                'expanded'=>true,
                'multiple'=>true,
                /*'attr' => array(
                    'class'=>'form-control',
                   # 'class'=>'selectpicker form-control',
                   # 'data-live-search'=>'true',
                   # 'data-actions-box'=>true
                )*/

            ));
        });



        $min = $this->em->createQueryBuilder()
            ->select("p.priceOnline")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            ->where(':category_id MEMBER OF p.categories')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->orderBy('p.priceOnline', 'asc')
            ->setParameter('category_id',$category->getId())
            ->getQuery()
            ->setMaxResults(1)
            ->getResult()
        ;

        $max = $this->em->createQueryBuilder()
            ->select("p.priceOnline")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            ->where(':category_id MEMBER OF p.categories')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->orderBy('p.priceOnline', 'desc')
            ->setParameter('category_id',$category->getId())
            ->getQuery()
            ->setMaxResults(1)
            ->getResult()

        ;
        $min = $min[0]['priceOnline']-1;
        $max = $max[0]['priceOnline']+1;


        $param = $this->requestStack->getCurrentRequest()->get('product_filter');
        if(isset($param["priceOnline"]["left_number"]) && isset($param["priceOnline"]["right_number"]) && strstr($param["priceOnline"]["left_number"],',')){
            $arr = explode(",",$param["priceOnline"]["left_number"]);
            $left = $arr[0];
            $right = $arr[1];
        }else{
            $left = $min;
            $right =$max;
        }


        ($left)?null:$left=$min;
        ($right)?null:$right=$max;


        $builder

            ->add('priceOnline', Filters\NumberRangeFilterType::class,array(
                'label'=>'sitebundle.pricerange',
                'right_number_options'=>array(
                    "data"=>($right)?$right:"0",
                    'condition_operator' => FilterOperands::OPERATOR_LOWER_THAN_EQUAL,
                    'attr' => array(
                        'class'=>'price-range-disabled',
                        'data-slider-value'=>"$max",

                    )
                ),
                'left_number_options'=>array(
                    "data"=>($left)?$left:"4000",
                    'condition_operator' => FilterOperands::OPERATOR_GREATER_THAN_EQUAL,
                    'attr' => array(
                        'class'=>'price-range',
                        'data-slider-min'=>"$min",
                        'data-slider-max'=>"$max",
                        'data-slider-step'=>"1",
                        'data-slider-value'=>  "[$left,$right]",

                    )
                )

            ))
            ->add('results', ChoiceType::class,array(
                'label'=>'sitebundle.filter.results',
                'apply_filter' => false,
                'choices'=>array(
                    10=>10,20=>20,30=>30,50=>50,100=>100
                )
            ))

            ->add('name', Filters\TextFilterType::class,array(
                'label'=>'sitebundle.productname',
            ))
            ->add('isSecondHand', Filters\BooleanFilterType::class,array(
                'label'=>'sitebundle.issecondhand',
            ));

    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering'), // avoid NotBlank() constraint-related message
            'category'=>null,
            'sort_key'=>null,
            'sort_direction'=>null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'product_filter';
    }

}