<?php
namespace SiteBundle\Filter;


use AppBundle\Entity\Brand;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\EntityRepository;

class ProductInBrandFilterType extends AbstractType
{


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
        $builder

            ->add('priceOnline', Filters\NumberRangeFilterType::class)
            ->add('name', Filters\TextFilterType::class)
            ->add('isSecondHand', Filters\BooleanFilterType::class)
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



            ))

            ;

    }



    public function getBlockPrefix()
    {
        return 'product_filter';
    }

}