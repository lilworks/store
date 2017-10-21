<?php
namespace SiteBundle\Filter;


use AppBundle\Entity\Brand;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class ProductInAllFilterType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('search', Filters\TextFilterType::class, array(
                'apply_filter' => function (QueryInterface $filterBuilder, $field, $values) {
                    if (!empty($values['value'])) {
                        $values = explode(' ',$values['value']);
                        $c = 0;
                        foreach($values as $value){
                            if($c==0){
                                $filterBuilder->getQueryBuilder()
                                    ->andWhere("p.name LIKE :value$c")
                                    ->orWhere("b.name LIKE :value$c")
                                    ->orWhere("c.name LIKE :value$c")
                                    ->orWhere("sc.name LIKE :value$c")
                                    ->setParameter('value'.$c, "%".$value."%")
                                ;
                            }else{
                                $filterBuilder->getQueryBuilder()
                                    ->orWhere("p.name LIKE :value$c")
                                    ->orWhere("b.name LIKE :value$c")
                                    ->orWhere("c.name LIKE :value$c")
                                    ->orWhere("sc.name LIKE :value$c")
                                    ->setParameter('value'.$c, "%".$value."%")
                                ;
                            }
                            $c++;
                        }
                    }
                },
                'mapped' => false,
            ))
            ->add('priceOnline', Filters\NumberRangeFilterType::class)
            ->add('isSecondHand', Filters\BooleanFilterType::class)



        ;

    }



    public function getBlockPrefix()
    {
        return 'product_filter';
    }

}