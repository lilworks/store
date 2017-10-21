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

class ProductInSuperCategoryFilterType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $supercategory = $options['data']['supercategory'];

        $categories = array();
        $brands = array();

        foreach($supercategory->getSupercategoriesCategories() as $superCategoryCategory){
            $category = $superCategoryCategory->getCategory();
            foreach( $category->getProducts() as $product){
                if(!isset($brands[$product->getBrand()->getId()])){
                    if($product->getIsPublished()){
                        array_push($brands,$product->getBrand()->getId());
                        foreach($product->getCategories() as $category){
                            array_push($categories,$category->getId());
                        }
                    }
                }
            }


        }

        $brands = array_unique($brands);
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
                        ->andWhere('b.isPublished = 1')
                        ->orderBy('b.name', 'ASC')
                        ->setParameter('categories',$categories)
                        ;
                },



            ))

            ->add('brand', Filters\EntityFilterType::class, array(
                'class'    =>  'LilWorksStoreBundle:Brand',
                'expanded'=>true,
                'multiple'=>true,
                'choice_label' => function ( $brand )  {

                    return $brand->getName();
                },

                'query_builder' => function (EntityRepository $er) use ($brands) {
                    return $er->createQueryBuilder('b')
                        ->where('b.id in (:brands)')
                        ->orderBy('b.name', 'ASC')
                        ->setParameter('brands',$brands)
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