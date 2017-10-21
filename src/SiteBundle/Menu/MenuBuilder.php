<?php
namespace SiteBundle\Menu;




use Knp\Menu\FactoryInterface;

class MenuBuilder
{
    private $factory;
    private $em;
    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory,\Doctrine\ORM\EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function createMainMenu(array $options)
    {



        $superCategories = $this->em->createQueryBuilder()
            ->from('LilWorksStoreBundle:SuperCategory','sc')
            ->select('sc')
            ->leftJoin('LilWorksStoreBundle:SuperCategoriesCategories','scc','with','scc.supercategory = sc.id')
            ->where('sc.isPublished = 1')
            ->having('COUNT(scc) > 0')
            ->groupBy('sc.id')
            ->orderBy('sc.pos','asc')
            ->getQuery()
            ->getResult()
        ;
/*
        $emptyCategories = $this->em->createQueryBuilder()
            ->from('LilWorksStoreBundle:Category','c')
            ->select('c.id')
            ->leftJoin('c.products','p')
            ->having('COUNT(p) = 0')
            ->getQuery()
            ->getArrayResult()
            ;
*/
        $countProductsInCategory = $this->em->createQueryBuilder()
            ->select('c.id,p.isPublished,c.isPublished,b.isPublished,COUNT(p) as pcount')
            ->from('LilWorksStoreBundle:Category','c')
            ->join('c.products','p')
            ->join('p.brand','b')
            ->where('c.isPublished = 1')
            ->andWhere('p.priceOnline IS NOT NULL')
            ->andWhere('p.isPublished = 1')
            ->andWhere('b.isPublished = 1')
            ->andHaving('COUNT(p) > 0')
            ->groupBy('c.id')
            ->getQuery()
            ->getArrayResult()
        ;

        $authorizedCategories = array();
        $categoriesProductCount = array();
        foreach($countProductsInCategory as $v){
            array_push($authorizedCategories,$v['id']);
            $categoriesProductCount[ $v['id'] ] = $v['pcount'];
        }

        $authorizedSuperCategories = array();

        foreach($superCategories as $superCategory){

            $publishedAuthorized =  $this->em->createQueryBuilder()
                ->select('sc')
                ->from('LilWorksStoreBundle:SuperCategory','sc')
                ->leftJoin('LilWorksStoreBundle:SuperCategoriesCategories','scc','with','scc.supercategory = sc.id')
                ->leftJoin('LilWorksStoreBundle:Category','c','with','scc.category = c.id')
                ->join('c.products','p')
                ->join('p.brand','b')
                ->where('sc.id = :id')
                ->andWhere('p.isPublished = 1')
                ->andWhere('b.isPublished = 1')
                ->andWhere('c.isPublished = 1')
                ->andWhere('c.id IN (:cat)')
                ->orderBy('scc.pos','asc')
                ->setParameter('id',$superCategory->getId())
                ->setParameter('cat',$authorizedCategories)
                ->setMaxResults(1)
                ->getQuery()
                ->getArrayResult()

            ;

            if(count($publishedAuthorized)>0){
                array_push($authorizedSuperCategories , $superCategory->getId() );
            }
        }


        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'navbar-nav ml-auto');
        $menu->setExtra('translation_domain', false);

        $i = 1;
        foreach($superCategories as $superCategory){
            if($i>=15){break;}


            if(in_array($superCategory->getId(),$authorizedSuperCategories)){





                if(count($superCategory->getSupercategoriesCategories())==1){

                    $coll = $superCategory->getSupercategoriesCategories();


                    $menu->addChild( $superCategory->getName(), array('route' => 'site_category','routeParameters' => array('tag' => $coll[0]->getCategory()->getTag())))
                        ->setAttribute('class','full-width nav-item no-dropdown')
                        ->setAttribute('dropdown', false)
                        ->setLinkAttribute('class',' nav-link ')
                        ->setExtra('translation_domain',false)
                    ;


                }else{
                    $menu->addChild( $superCategory->getName(), array('route' => 'site_homepage'))
                        ->setAttribute('class','full-width nav-item')
                        ->setAttribute('dropdown', true)
                        ->setExtra('translation_domain',false)
                    ;

                    $menu[$superCategory->getName()]->addChild(
                        $superCategory->getName(),
                        array('route' => 'site_supercategory', 'routeParameters' => array('tag' => $superCategory->getTag())))
                        ->setAttribute('isfirstcat', true)
                        ->setLinkAttribute('class', 'dropdown-item-first')
                        ->setExtra('translation_domain',false)
                    ;
                    if($superCategory->getPictureName()){
                        $menu[$superCategory->getName()][$superCategory->getName()]->setAttribute('icon', 'supercategory/' . $superCategory->getPictureName());
                    }
                    foreach($superCategory->getSupercategoriesCategories() as $supercategoryCategory) {
                        $category = $supercategoryCategory->getCategory();
                        if(in_array($category->getId(),$authorizedCategories)){
                            if ($category->getIsPublished() == 1) {
                                $childName =  $category->getName() . "(".$categoriesProductCount[$category->getId()] .")";
                                $menu[$superCategory->getName()]->addChild(
                                    $childName,
                                    array('route' => 'site_category', 'routeParameters' => array('tag' => $category->getTag())))
                                    ->setLinkAttribute('class', 'dropdown-item')
                                    ->setExtra('translation_domain',false)
                                ;

                                if($category->getPictureName()){
                                    $menu[$superCategory->getName()][$childName]->setAttribute('icon', 'category/' . $category->getPictureName());
                                }
                            }
                        }


                    }
                }


                $i++;


            }



        }



        return $menu;
    }
}