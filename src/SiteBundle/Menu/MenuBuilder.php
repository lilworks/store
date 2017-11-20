<?php
namespace SiteBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use SiteBundle\Event\ConfigureSiteMenuEvent;

class MenuBuilder
{

    private $factory;
    private $em;
    private $token_storage;
    private $container;
    private $requestStack;
    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory,\Doctrine\ORM\EntityManager $em,TokenStorage $token_storage,$container)
    {
        $this->container = $container;
        $this->factory = $factory;
        $this->em = $em;
        $this->token_storage = $token_storage;
        $this->requestStack = $this->container->get('request_stack');

        if( !is_null($this->token_storage->getToken()) && is_object($this->token_storage->getToken()->getUser()) )
            $this->user = $this->token_storage->getToken()->getUser();
    }


    public function createUserCustomerMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setExtra('translation_domain','messages');
        $menu->setAttribute('class','list-unstyled');
        $menu->setChildrenAttribute('class','list-inline');

        $user = $this->token_storage->getToken()->getUser();


        if(is_object($user)){

            $customerShow = $menu->addChild('sitebundle.menu.customer.show', array('route' => 'site_customer'));
            $customerShow->setAttribute('class','list-inline-item');
            $customerShow->setLinkAttribute('role','button');
            $customerShow->setLinkAttribute('class','btn btn-sm btn-secondary');
            $customerShow->setLinkAttribute('i','fa fa-eye');

            $conversations = $this->em->createQueryBuilder()
                ->from('LilWorksStoreBundle:Conversation','c')
                ->select('c')
                ->leftJoin('c.user','u')
                ->where('u.id = :user_id')
                ->orWhere('u.email LIKE :email')
                ->setParameter('user_id',$user->getId())
                ->setParameter('email',$user->getEmail())
                ->getQuery()
                ->getResult()
            ;
            $subscriber = $this->em->createQueryBuilder()
                ->from('LilWorksStoreBundle:Subscriber','s')
                ->select('s')
                ->leftJoin('s.user','u')
                ->where('u.id = :user_id')
                ->orWhere('u.email LIKE :email')
                ->setParameter('user_id',$user->getId())
                ->setParameter('email',$user->getEmail())
                ->getQuery()
                ->getResult()
            ;


            $userEdit = $menu->addChild('sitebundle.menu.user.profile', array('route' => 'fos_user_profile_edit'));
            $userEdit->setAttribute('class','list-inline-item');
            $userEdit->setLinkAttribute('role','button');
            $userEdit->setLinkAttribute('class','btn btn-sm btn-secondary');
            $userEdit->setLinkAttribute('i','fa fa-user-o');

            if(!is_null($user->getCustomer())){
                $orders = $this->em->createQueryBuilder()
                    ->from('LilWorksStoreBundle:Order','o')
                    ->select('o')
                    ->leftJoin('o.customer','c')
                    ->where('c.id = :customer_id')
                    ->setParameter('customer_id',$user->getCustomer()->getId())
                    ->getQuery()
                    ->getResult()
                ;





                $customerEdit = $menu->addChild('sitebundle.menu.customer.edit', array('route' => 'site_customer_edit'));
                $customerEdit->setAttribute('class','list-inline-item');
                $customerEdit->setLinkAttribute('role','button');
                $customerEdit->setLinkAttribute('class','btn btn-sm btn-secondary');
                $customerEdit->setLinkAttribute('i','fa fa-user-circle-o');



                if(count($orders)>0){
                    $orders = $menu->addChild('sitebundle.menu.customer.orders', array('route' => 'site_orders'));
                    $orders->setAttribute('class','list-inline-item');
                    $orders->setLinkAttribute('role','button');
                    $orders->setLinkAttribute('class','btn btn-sm btn-secondary');
                    $orders->setLinkAttribute('i','fa fa-handshake-o');

                    $this->container->get('event_dispatcher')->dispatch(
                        ConfigureSiteMenuEvent::CONFIGURE,
                        new ConfigureSiteMenuEvent( $this->factory,
                            $orders ,
                            'orders' ,
                            $this->requestStack->getCurrentRequest()->get('order_id')
                        )
                    );


                }else{
                    $orders = $menu->addChild('sitebundle.menu.customer.orders', array('route' => 'site_orders'));
                    $orders->setAttribute('class','list-inline-item');
                    $orders->setLinkAttribute('role','button');
                    $orders->setLinkAttribute('class','btn btn-sm btn-secondary disabled');
                    $orders->setLinkAttribute('i','fa fa-handshake-o');
                }


            }

            $conversation = $menu->addChild('sitebundle.menu.user.conversations', array('route' => 'site_conversations'));
            $conversation->setAttribute('class','list-inline-item');
            $conversation->setLinkAttribute('role','button');
            $conversation->setLinkAttribute('class','btn btn-sm btn-secondary');
            $conversation->setLinkAttribute('i','fa fa-comments-o');

            $this->container->get('event_dispatcher')->dispatch(
                ConfigureSiteMenuEvent::CONFIGURE,
                new ConfigureSiteMenuEvent( $this->factory,
                    $conversation ,
                    'conversations' ,
                    $this->requestStack->getCurrentRequest()->get('conversation_id')
                )
            );


            if(count($subscriber)>0){
                $subscriber = $menu->addChild('sitebundle.menu.user.subscriber', array('route' => 'site_subscriber_unsubscribe'));
                $subscriber->setLabel('sitebundle.menu.user.unsubscribe');
                $subscriber->setLinkAttribute('class','btn btn-sm btn-secondary   btn-unsubscribe');
            }else{
                $subscriber = $menu->addChild('sitebundle.menu.user.subscriber', array('route' => 'site_subscriber_subscribe'));
                $subscriber->setLabel('sitebundle.menu.user.subscribe');
                $subscriber->setLinkAttribute('class','btn btn-sm btn-secondary btn-subscribe');
            }

            $subscriber->setAttribute('class','list-inline-item');
            $subscriber->setLinkAttribute('role','button');
            $subscriber->setLinkAttribute('i','fa fa-newspaper-o');

        }
        return $menu;

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