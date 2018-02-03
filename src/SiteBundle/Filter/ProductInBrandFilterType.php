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


class ProductInBrandFilterType extends AbstractType
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
        $brand = $options['data']['brand'];
        $categories = array();
        foreach( $brand->getProducts() as $product){
            if($product->getIsPublished()){
                foreach( $product->getCategories() as $category){
                    array_push($categories,$category->getId());
                }
            }
        }
        $categories = array_unique($categories);

        $rtags = $this->em->createQueryBuilder()
            ->select("t.id as id ,COUNT(p) as countp")

            ->from("LilWorksStoreBundle:Tag","t")
            ->join("t.products","p")
            ->where("p.isPublished = 1")
            ->andWhere('p.priceOnline IS NOT NULL ')
            ->andWhere('p.brand = :brand_id')
            ->having('COUNT(p)>0')
            ->groupBy('t.id')
            ->setParameter('brand_id',$brand->getId())
            ->getQuery()
            ->getArrayResult()
        ;

        $tags = array();
        $productsInTag = array();
        foreach($rtags as $tag){
            array_push($tags,$tag["id"]);
            $productsInTag[$tag["id"]]=$tag["countp"];
        }

        $min = $this->em->createQueryBuilder()
            ->select("p.priceOnline")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            ->where('b.id = :brand_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->orderBy('p.priceOnline', 'asc')
            ->setParameter('brand_id',$brand->getId())
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;

        $max = $this->em->createQueryBuilder()
            ->select("p.priceOnline")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            ->where('b.id = :brand_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->orderBy('p.priceOnline', 'desc')
            ->setParameter('brand_id',$brand->getId())
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()

        ;

        $min = $min['priceOnline']-1;
        $max = $max['priceOnline']+1;


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


            if(count($tags)>0){
                $builder
                    ->add('tags', Filters\EntityFilterType::class, array(
                        #'empty_data'=>$tags,
                        'label'    =>  'sitebundle.tag',
                        'class'    =>  'LilWorksStoreBundle:Tag',
                        'choice_label' => function ( $tag ) use ($productsInTag){
                            return $tag->getName() . "(".$productsInTag[$tag->getId()].")" ;
                        },
                        'query_builder' => function (EntityRepository $er) use ($tags) {
                            return $er->createQueryBuilder('t')
                                ->where('t.id IN (:ids)')
                                ->setParameter('ids',$tags)
                                ;
                        },
                        'expanded'=>true,
                        'multiple'=>true,
                        'attr' => array(
                            'class'=>'form-control',
                            #'class'=>'selectpicker form-control',
                            #'data-live-search'=>'true',
                            #'data-actions-box'=>true
                        )

                    ));
            }
        $builder
        ->add('categories', Filters\EntityFilterType::class, array(
                'class'    =>  'LilWorksStoreBundle:Category',
                'expanded'=>true,
                'multiple'=>true,
                'choice_label' => function ( $category ) {
                    return $category->getName();
                },

                'query_builder' => function (EntityRepository $er) use ($categories) {
                    return $er->createQueryBuilder('b')
                        ->where('b.id in (:categories)')
                        ->orderBy('b.name', 'ASC')
                        ->setParameter('categories',$categories)
                        ;
                },
                'attr' => array(
                    'class'=>'form-control',
                    #'class'=>'selectpicker form-control',
                    #'data-live-search'=>'true',
                    #'data-actions-box'=>true
                )

            ))

            ;

    }



    public function getBlockPrefix()
    {
        return 'product_filter';
    }

}