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
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class ProductInAllFilterType extends AbstractType
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

        $min = $this->em->createQueryBuilder()
            ->select("p.priceOnline")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            #->where('b.id = :brand_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->orderBy('p.priceOnline', 'asc')
            #->setParameter('brand_id',$brand->getId())
            ->getQuery()
            ->setMaxResults(1)
            ->getResult()
        ;

        $max = $this->em->createQueryBuilder()
            ->select("p.priceOnline")
            ->from("LilWorksStoreBundle:Product","p")
            ->join("p.brand","b")
            #->where('b.id = :brand_id')
            ->andWhere('p.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('b.isPublished = 1')
            ->orderBy('p.priceOnline', 'desc')
            #->setParameter('brand_id',$brand->getId())
            ->getQuery()
            ->setMaxResults(1)
            ->getResult()

        ;
        $min = $min[0]['priceOnline']-1;
        $min=0;
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
                        'class'=>'price-range ',
                        'data-slider-min'=>"$min",
                        'data-slider-max'=>"$max",
                        'data-slider-step'=>"1",
                        'data-slider-value'=>  "[$left,$right]",

                    )
                )

            ))
            /*
            ->add('results', ChoiceType::class,array(
                'label'=>'sitebundle.filter.results',
                'apply_filter' => false,
                'choices'=>array(
                    10=>10,20=>20,30=>30,50=>50,100=>100
                )
            ))
*/
                /*
            ->add('name', Filters\TextFilterType::class,array(
                'label'=>'sitebundle.productname',
            ))
                */
            ->add('isSecondHand', Filters\BooleanFilterType::class,array(
                'label'=>'sitebundle.issecondhand',
            ))
            ->add('search', Filters\TextFilterType::class, array(
                'label'=>'sitebundle.productname',
                'mapped' => false,
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    }

                    $words = explode(' ', $values["value"]);

                    $parameters = array();
                    $filterQueries = array();
                    if(count($words)>0){
                        foreach($words as $k=>$word){
                            $parameters["word$k"] = $word;
                        }
                    }else{
                        $parameters["word0"] = $words;
                    }
                    foreach($parameters as $k=>$word){
                        array_push($filterQueries,
                            $filterQuery->getExpr()->eq("REGEXP(CONCAT(CONCAT(b.name,'|',p.name),'|',c.name), :$k)", 1)

                        );
                    }
                    $andX = $filterQuery->getExpr()->andX();
                    foreach($filterQueries as $q) {
                        $andX->add($q);
                    }
                    $expression = $andX;
                    return $filterQuery->createCondition($expression, $parameters);
                }
            ))
            ->add('tags', Filters\EntityFilterType::class, array(
                'class'    =>  'LilWorksStoreBundle:Tag',
                'expanded'=>false,
                'multiple'=>true,
                'choice_label' => function ( $tag ) {
                    return $tag->getName();
                },
                'attr' => array(
                    'class'=>'selectpicker form-control',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"180px"
                ),
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('t')
                        #->where('b.id in (:categories)')
                        ->orderBy('t.name', 'ASC')
                        #->setParameter('categories',$categories)
                        ;
                },



            ))
            ->add('categories', Filters\EntityFilterType::class, array(
                'class'    =>  'LilWorksStoreBundle:Category',
                'choice_label' => function ( $category ) {
                    return $category->getName();
                },
                /*'attr' => array(
                    'class'=>'form-control',
                ),*/
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('c')
                        #->where('b.id in (:categories)')
                        ->orderBy('c.name', 'ASC')
                        #->setParameter('categories',$categories)
                        ;
                },
                'expanded' => false ,
                'multiple' => true,
                'attr' => array(
                    'class'=>'selectpicker form-control',
                    'data-live-search'=>'true',
                    'data-actions-box'=>true,
                    'data-width'=>"180px"
                )


            ))

        ;

    }



    public function getBlockPrefix()
    {
        return 'product_filter';
    }

}