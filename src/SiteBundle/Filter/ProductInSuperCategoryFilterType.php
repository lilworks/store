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


class ProductInSuperCategoryFilterType extends AbstractType
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

        $supercategory = $options['data']['supercategory'];


        $rbrands = $this->em->createQueryBuilder()
            ->select("b.id as id ,COUNT(p) as countp")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            ->join('p.categories','c')
            ->join('c.supercategories_categories','scc')
            ->join('scc.supercategory','sc')
            ->where('sc.id = :supercategory_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->having('COUNT(p)>0')
            ->groupBy('b.id')
            ->setParameter('supercategory_id',$supercategory->getId())
            ->getQuery()
            ->getArrayResult()
        ;
        $brands = array();
        $productsInBrand = array();
        foreach($rbrands as $brand){
            array_push($brands,$brand["id"]);
            $productsInBrand[$brand["id"]]=$brand["countp"];
        }

        $rtags = $this->em->createQueryBuilder()
            ->select("t.id as id ,COUNT(p) as countp")
            ->from("LilWorksStoreBundle:Tag","t")
            ->join("t.products","p")
            ->join("p.categories","c")
            ->where("p.isPublished = 1")
            ->andWhere('p.priceOnline IS NOT NULL ')
            ->andWhere(':supercategory_id MEMBER OF c.supercategories_categories')
            ->having('COUNT(p)>0')
            ->groupBy('t.id')
            ->setParameter('supercategory_id',$supercategory->getId())
            ->getQuery()
            ->getArrayResult()
        ;

        $tags = array();
        $productsInTag = array();
        foreach($rtags as $tag){
            array_push($tags,$tag["id"]);
            $productsInTag[$tag["id"]]=$tag["countp"];
        }

        $rcategories = $this->em->createQueryBuilder()
            ->select("c.id as id ,COUNT(p) as countp")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            ->join('p.categories','c')
            ->join('c.supercategories_categories','scc')
            ->join('scc.supercategory','sc')
            ->where('sc.id = :supercategory_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->having('COUNT(p)>0')
            ->groupBy('c.id')
            ->setParameter('supercategory_id',$supercategory->getId())
            ->getQuery()
            ->getArrayResult()
        ;
        $categories = array();
        $productsInCategory = array();
        foreach($rcategories as $category){
            array_push($categories,$category["id"]);
            $productsInCategory[$category["id"]]=$category["countp"];
        }


        $min = $this->em->createQueryBuilder()
            ->select("p.priceOnline")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            ->join('p.categories','c')
            ->join('c.supercategories_categories','scc')
            ->join('scc.supercategory','sc')
            ->where('sc.id = :supercategory_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->orderBy('p.priceOnline', 'asc')
            ->setParameter('supercategory_id',$supercategory->getId())
            ->getQuery()
            ->setMaxResults(1)
            ->getResult()
        ;

        $max = $this->em->createQueryBuilder()
            ->select("p.priceOnline")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            ->join('p.categories','c')
            ->join('c.supercategories_categories','scc')
            ->join('scc.supercategory','sc')
            ->where('sc.id = :supercategory_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->orderBy('p.priceOnline', 'desc')
            ->setParameter('supercategory_id',$supercategory->getId())
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
                'label'    =>  'sitebundle.category',
                'class'    =>  'LilWorksStoreBundle:Category',
                'expanded'=>true,
                'multiple'=>true,
                'choice_label' => function ( $category ) use ($productsInCategory){
                    return $category->getName() . "(".$productsInCategory[$category->getId()].")" ;
                },
                'attr' => array(
                    'class'=>'form-control',
                ),
                'query_builder' => function (EntityRepository $er) use ($categories) {
                    return $er->createQueryBuilder('c')
                        ->where('c.id IN (:ids)')
                        ->andWhere('c.isPublished = 1')
                        ->orderBy('c.name','asc')
                        ->setParameter('ids',$categories)
                        ;
                },
                'attr' => array(
                    'class'=>'form-control',
                    #'class'=>'selectpicker form-control',
                    #'data-live-search'=>'true',
                    #'data-actions-box'=>true
                )



            ))

            ->add('brand', Filters\EntityFilterType::class, array(
                'label'    =>  'sitebundle.brand',
                'class'    =>  'LilWorksStoreBundle:Brand',
                'expanded'=>true,
                'multiple'=>true,
                'choice_label' => function ( $brand ) use ($productsInBrand){
                    return $brand->getName() . "(".$productsInBrand[$brand->getId()].")" ;
                },
                'attr' => array(
                    'class'=>'form-control',
                ),
                'query_builder' => function (EntityRepository $er) use ($brands) {
                    return $er->createQueryBuilder('b')
                        ->where('b.id IN (:ids)')
                        ->andWhere('b.isPublished = 1')
                        ->orderBy('b.name','asc')
                        ->setParameter('ids',$brands)
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