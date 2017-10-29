<?php
namespace AppBundle\Menu;

use AppBundle\Event\ConfigureStoreMenuEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;

class StoreBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    public function build(FactoryInterface $factory ,array $options)
    {

        $this->container->getParameter('mode');
        $this->container->getParameter('context');

        $this->requestStack = $this->container->get('request_stack');



        if($options["context"]=="sideBar"){
            $attr = array(
                0=>array(
                    'link'=>array(),
                    'child'=>array('class'=>'list'),
                    'curr'=>array(),
                ),
                1=>array(
                    'link'=>array('class'=>'list-item '),
                    'child'=>array('class'=>'list'),
                    'curr'=>array('class'=>'list-item')
                ),
                2=>array(
                    'link'=>array(),
                    'child'=>array(),
                    'curr'=>array()
                ),
            );
        }elseif($options["context"]=="topBar"  ){

            $attr = array(
                0=>array(
                    'link'=>array(),
                    'child'=>array('class'=>'navbar-nav mr-auto'),
                    'curr'=>array(''),
                ),
                1=>array(
                    'link'=>array('class'=>'nav-link'),
                    'child'=>array('class'=>''),
                    'curr'=>array('class'=>'nav-item')
                ),
                2=>array(
                    'link'=>array('class'=>''),
                    'child'=>array(),
                    'curr'=>array()
                ),
            );

        }elseif( $options["context"] == "content" ){
            $attr = array(
                0=>array(
                    'link'=>array(),
                    'child'=>array('class'=>'btn-group btn-group-sm' ),
                    'curr'=>array(),
                ),
                1=>array(
                    'link'=>array('class'=>'btn btn-primary btn-sm'),
                    'child'=>array('class'=>'btn-group btn-group-sm'),
                    'curr'=>array('class'=>'btn btn-sm')
                ),
                2=>array(
                    'link'=>array(),
                    'child'=>array(),
                    'curr'=>array()
                ),
            );
        }elseif( $options["context"] == "portal" ){
            $attr = array(
                0=>array(
                    'link'=>array(),
                    'child'=>array('class'=>'list'),
                    'curr'=>array(),
                ),
                1=>array(
                    'link'=>array('class'=>'list-item '),
                    'child'=>array('class'=>'list'),
                    'curr'=>array('class'=>'list-item')
                ),
                2=>array(
                    'link'=>array(),
                    'child'=>array(),
                    'curr'=>array()
                ),
            );
        }

        $this->factory = $factory;




        $menu = $this->factory->createItem('root',array(
                'childrenAttributes'=>$attr[0]['child'],
        ));

        $menu->addChild('storebundle.menu.storehome', array('route' => 'site_homepage',
            'attributes'=>$attr[1]['curr'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        /*
                $menu->addChild('storebundle.menu.adminhome', array('route' => 'lilworks_store_homepage',
                    'attributes'=>$attr[1]['curr'],
                    'linkAttributes'=>$attr[1]['link'],

                ));
                */
        // level 0
        $userMenuCatPortal = $menu->addChild('storebundle.menu.cat.user.portal', array(
            'route' => 'portal_user',
            'attributes'=>$attr[1]['curr'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $userMenuCatPortal->setAttribute('i','fa fa-user');
        $userMenuCatPortal->setChildrenAttribute('class',$attr[1]['child']['class']);

        //level 1
        $customerMenuCat = $userMenuCatPortal->addChild('storebundle.menu.customer', array(
            #'route' => 'portal_user',
            'attributes'=>$attr[1]['curr'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $customerMenuCat->setAttribute('i','fa fa-handshake-o');
        $customerMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $customerMenuCat ,
                'customer' ,
                $this->requestStack->getCurrentRequest()->get('customer_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );

        $userMenuCat =  $userMenuCatPortal->addChild('storebundle.menu.user', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $userMenuCat->setAttribute('i','fa fa-user');
        $userMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $userMenuCat ,
                'user' ,
                $this->requestStack->getCurrentRequest()->get('user_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );

        // level 1
        $sessionMenuCat =  $userMenuCatPortal->addChild('storebundle.menu.session', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $sessionMenuCat->setAttribute('i','fa fa-users');
        $sessionMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $sessionMenuCat ,
                'session' ,
                $this->requestStack->getCurrentRequest()->get('session_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );


        //////// END USER
        // level 0
        $documentMenuCatPortal = $menu->addChild('storebundle.menu.cat.document.portal', array(
            'route' => 'portal_document',
            'attributes'=>$attr[1]['curr'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $documentMenuCatPortal->setAttribute('i','fa fa-file-o');
        $documentMenuCatPortal->setChildrenAttribute('class',$attr[1]['child']['class']);
        // level 1
        $orderMenuCat =  $documentMenuCatPortal->addChild('storebundle.menu.order', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $orderMenuCat->setAttribute('i','fa fa-file-o');
        $orderMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $orderMenuCat ,
                'order' ,
                $this->requestStack->getCurrentRequest()->get('order_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $depositMenuCat =  $documentMenuCatPortal->addChild('storebundle.menu.depositsale', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $depositMenuCat->setAttribute('i','fa fa-gavel');
        $depositMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $depositMenuCat ,
                'depositSale' ,
                $this->requestStack->getCurrentRequest()->get('depositsale_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $couponMenuCat =  $documentMenuCatPortal->addChild('storebundle.menu.coupon', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $couponMenuCat->setAttribute('i','fa fa-university');
        $couponMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $couponMenuCat ,
                'coupon' ,
                $this->requestStack->getCurrentRequest()->get('coupon_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );

        //////// END DOCUMENT
        // level 0
        $productMenuCatPortal = $menu->addChild('storebundle.menu.cat.product.portal', array(
            'route' => 'portal_document',
            'attributes'=>$attr[1]['curr'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $productMenuCatPortal->setAttribute('i','fa fa-cubes');
        $productMenuCatPortal->setChildrenAttribute('class',$attr[1]['child']['class']);
        // level 1
        $productMenuCat =  $productMenuCatPortal->addChild('storebundle.menu.product', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $productMenuCat->setAttribute('i','fa fa-cubes');
        $productMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $productMenuCat ,
                'product' ,
                $this->requestStack->getCurrentRequest()->get('product_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $brandMenuCat =  $productMenuCatPortal->addChild('storebundle.menu.brand', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $brandMenuCat->setAttribute('i','fa fa-copyright');
        $brandMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $brandMenuCat ,
                'brand' ,
                $this->requestStack->getCurrentRequest()->get('brand_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $categoryMenuCat =  $productMenuCatPortal->addChild('storebundle.menu.category', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $categoryMenuCat->setAttribute('i','fa fa-bullseye');
        $categoryMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $categoryMenuCat ,
                'category' ,
                $this->requestStack->getCurrentRequest()->get('category_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        $menu->setExtra('translation_domain', 'LilWorksStoreBundle');
        // level 1
        $supercategoryMenuCat =  $productMenuCatPortal->addChild('storebundle.menu.supercategory', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $supercategoryMenuCat->setAttribute('i','fa fa-object-group');
        $supercategoryMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $supercategoryMenuCat ,
                'supercategory' ,
                $this->requestStack->getCurrentRequest()->get('supercategory_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $warrantyMenuCat =  $productMenuCatPortal->addChild('storebundle.menu.warranty', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $warrantyMenuCat->setAttribute('i','fa fa-wrench');
        $warrantyMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $warrantyMenuCat ,
                'warranty' ,
                $this->requestStack->getCurrentRequest()->get('warranty_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $tagMenuCat =  $productMenuCatPortal->addChild('storebundle.menu.tag', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $tagMenuCat->setAttribute('i','fa fa-tag');
        $tagMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $tagMenuCat ,
                'tag' ,
                $this->requestStack->getCurrentRequest()->get('tag_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );

        // level 1
        $docfileMenuCat =  $productMenuCatPortal->addChild('storebundle.menu.docfile', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $docfileMenuCat->setAttribute('i','fa fa-book');
        $docfileMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $docfileMenuCat ,
                'docfile' ,
                $this->requestStack->getCurrentRequest()->get('docfile_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );





        //////// END PRODUCT
        // level 0
        $paymentMenuCatPortal = $menu->addChild('storebundle.menu.cat.payment.portal', array(
            'route' => 'portal_payment',
            'attributes'=>$attr[1]['curr'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $paymentMenuCatPortal->setAttribute('i','fa fa-eur');
        $paymentMenuCatPortal->setChildrenAttribute('class',$attr[1]['child']['class']);
        // level 1
        $paymentMenuCat =  $paymentMenuCatPortal->addChild('storebundle.menu.paymentmethod', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $paymentMenuCat->setAttribute('i','fa fa-credit-card');
        $paymentMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $paymentMenuCat ,
                'paymentmethod' ,
                $this->requestStack->getCurrentRequest()->get('paymentmethod_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $taxMenuCat =  $paymentMenuCatPortal->addChild('storebundle.menu.tax', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $taxMenuCat->setAttribute('i','fa fa-pie-chart');
        $taxMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $taxMenuCat ,
                'tax' ,
                $this->requestStack->getCurrentRequest()->get('tax_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        //////// END PAYMENT
        // level 0
        $shippingMenuCatPortal = $menu->addChild('storebundle.menu.cat.shipping.portal', array(
            'route' => 'portal_payment',
            'attributes'=>$attr[1]['curr'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $shippingMenuCatPortal->setAttribute('i','fa fa-truck');
        $shippingMenuCatPortal->setChildrenAttribute('class',$attr[1]['child']['class']);
        // level 1
        $shippingMenuCat =  $shippingMenuCatPortal->addChild('storebundle.menu.shippingmethod', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $shippingMenuCat->setAttribute('i','fa fa-truck');
        $shippingMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $shippingMenuCat ,
                'shippingmethod' ,
                $this->requestStack->getCurrentRequest()->get('shippingmethod_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $countryMenuCat =  $shippingMenuCatPortal->addChild('storebundle.menu.country', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $countryMenuCat->setAttribute('i','fa fa-flag');
        $countryMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $countryMenuCat ,
                'country' ,
                $this->requestStack->getCurrentRequest()->get('country_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        //////// END Shipping



        // level 0
        $configMenuCatPortal = $menu->addChild('storebundle.menu.cat.config.portal', array(
            'route' => 'portal_config',
            'attributes'=>$attr[1]['curr'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $configMenuCatPortal->setAttribute('i','fa fa-cog');
        $configMenuCatPortal->setChildrenAttribute('class',$attr[1]['child']['class']);
        // level 1
        $textMenuCat =  $configMenuCatPortal->addChild('storebundle.menu.text', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $textMenuCat->setAttribute('i','fa fa-font');
        $textMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $textMenuCat ,
                'text' ,
                $this->requestStack->getCurrentRequest()->get('text_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        $conversationMenuCat =  $configMenuCatPortal->addChild('storebundle.menu.conversation', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $conversationMenuCat->setAttribute('i','fa fa-comments');
        $conversationMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $conversationMenuCat ,
                'conversation' ,
                $this->requestStack->getCurrentRequest()->get('conversation_id'),
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        // level 1
        if( $this->container->getParameter('mode') == "master"){
        $syncroMenuCat =  $configMenuCatPortal->addChild('storebundle.menu.syncro', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $syncroMenuCat->setAttribute('i','fa fa-arrows-h');
        $syncroMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $syncroMenuCat ,
                'syncro' ,
                null,
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        }
        // level 1
        $annonceMenuCat =  $configMenuCatPortal->addChild('storebundle.menu.annonce', array(
            #'route' => 'user_index',
            'attributes'=>$attr[1]['curr'],
            'childrenAttributes'=>$attr[1]['child'],
            'linkAttributes'=>$attr[1]['link'],
        ));
        $annonceMenuCat->setAttribute('i','fa fa-bullhorn');
        $annonceMenuCat->setChildrenAttribute('class',$attr[1]['child']['class']);
        $this->container->get('event_dispatcher')->dispatch(
            ConfigureStoreMenuEvent::CONFIGURE,
            new ConfigureStoreMenuEvent( $factory,
                $annonceMenuCat ,
                'annonce' ,
                null,
                array('context'=> $options["context"],  'link'=>$attr[1]['link'],'curr'=>$attr[1]['curr'])
            )
        );
        $menu->setExtra('translation_domain', 'LilWorksStoreBundle');



        return $menu;
    }



}