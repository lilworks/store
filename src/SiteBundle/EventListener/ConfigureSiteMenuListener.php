<?php
namespace SiteBundle\EventListener;

use SiteBundle\Event\ConfigureSiteMenuEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ConfigureSiteMenuListener
{
    private $em;
    private $requestStack;
    private $target;
    private $options;
   /* private $config = array(

        'ICO_INDEX'=>'fa fa-list',
        'ICO_SHOW'=>'fa fa-eye',
        'ICO_NEW'=>'fa fa-plus',
        'ICO_EDIT'=>'fa fa-pencil',
        'ICO_DELETE'=>'fa fa-trash',
        'ICO_PDF'=>'fa fa-file-pdf',
        'ICO_IMPORT'=>'fa fa-upload',
        'ICO_EXPORT'=>'fa fa-download',


        'BTN_GENERAL'=>'btn btn-sm',
        'BTN_INDEX'=>'btn-info',
        'BTN_SHOW'=>'btn-info',
        'BTN_NEW'=>'btn-primary',
        'BTN_EDIT'=>'btn-primary',
        'BTN_DELETE'=>'btn-danger btn-delete',
        'BTN_PDF'=>'btn-primary',
        'BTN_IMPORT'=>'btn-primary',
        'BTN_EXPORT'=>'btn-primary',

    );*/

    public function __construct(\Doctrine\ORM\EntityManager $em,RequestStack $requestStack){
        $this->em = $em;
        $this->requestStack = $requestStack;
    }
    /**
     * @param \SiteBundle\Event\ConfigureSiteMenuEvent $event
     */
    public function onMenuConfigure( ConfigureSiteMenuEvent $event)
    {
        $fc = $event->getTarget();
        $this->target = $event->getTarget();
        $this->options = $event->getOptions();
        $menu = $event->getMenu();
        $id = $event->getId();



        if($this->target == 'conversations'){

            $conversationIndex = $menu->addChild('sitebundle.menu.conversations',array('route'=>'site_conversations'));
            $conversationIndex->setAttribute('class','list-inline-item');
            $conversationIndex->setLinkAttribute('role','button');
            $conversationIndex->setLinkAttribute('class','btn btn-sm btn-secondary');
            $conversationIndex->setLinkAttribute('i','fa fa-list');

            $conversationNew = $menu->addChild('sitebundle.menu.conversations.new',array('route'=>'site_conversations_new'));
            $conversationNew->setAttribute('class','list-inline-item');
            $conversationNew->setLinkAttribute('role','button');
            $conversationNew->setLinkAttribute('class','btn btn-sm btn-secondary');
            $conversationNew->setLinkAttribute('i','fa fa-plus');

            if($id){
                $conversationNewMessage = $menu->addChild('sitebundle.menu.conversations.newmessage', array('route' => 'site_conversations_newmessage', 'routeParameters' => array('conversation_id' => $id)));
                $conversationNewMessage->setAttribute('class','list-inline-item');
                $conversationNewMessage->setLinkAttribute('role','button');
                $conversationNewMessage->setLinkAttribute('class','btn btn-sm btn-secondary');
                $conversationNewMessage->setLinkAttribute('i','fa fa-pencil');

                $conversationRemove = $menu->addChild('sitebundle.menu.conversations.remove', array('route' => 'site_conversations_remove', 'routeParameters' => array('conversation_id' => $id)));
                $conversationRemove->setAttribute('class','list-inline-item');
                $conversationRemove->setLinkAttribute('role','button');
                $conversationRemove->setLinkAttribute('class','btn btn-sm btn-danger btn-delete');
                $conversationRemove->setLinkAttribute('i','fa fa-trash');
            }


        }

        if($this->target == 'orders'){


            $orderIndex = $menu->addChild('sitebundle.menu.orders',array('route'=>'site_orders'));
            $orderIndex->setAttribute('class','list-inline-item');
            $orderIndex->setLinkAttribute('role','button');
            $orderIndex->setLinkAttribute('class','btn btn-sm btn-secondary');
            $orderIndex->setLinkAttribute('i','fa fa-handshake-o');


            if($id){
                $order = $this->em->getRepository('LilWorksStoreBundle:Order')->find($id);

                if( $order->getPayed() != $order->getTot() && $order->getPayed() > 0 ){
                    $orderPay = $menu->addChild('sitebundle.menu.orders',array('route'=>'site_order_pay', 'routeParameters' => array('order_id' => $id)));
                    $orderPay->setAttribute('class','list-inline-item');
                    $orderPay->setLinkAttribute('role','button');
                    $orderPay->setLinkAttribute('class','btn btn-sm btn-secondary');
                    $orderPay->setLinkAttribute('i','fa fa-euro');
                }

                $orderShow = $menu->addChild('sitebundle.menu.orders',array('route'=>'site_order_show', 'routeParameters' => array('order_id' => $id)));
                $orderShow->setAttribute('class','list-inline-item');
                $orderShow->setLinkAttribute('role','button');
                $orderShow->setLinkAttribute('class','btn btn-sm btn-secondary');
                $orderShow->setLinkAttribute('i','fa fa-eye');


                $orderEdit = $menu->addChild('sitebundle.menu.orders.edit', array('route' => 'site_order_edit', 'routeParameters' => array('order_id' => $id)));
                $orderEdit->setAttribute('class','list-inline-item');
                $orderEdit->setLinkAttribute('role','button');
                $orderEdit->setLinkAttribute('class','btn btn-sm btn-secondary');
                $orderEdit->setLinkAttribute('i','fa fa-pencil');

                if($order->getPayed() == 0){
                    $orderRemove = $menu->addChild('sitebundle.menu.orders.remove', array('route' => 'site_order_remove', 'routeParameters' => array('order_id' => $id)));
                    $orderRemove->setAttribute('class','list-inline-item');
                    $orderRemove->setLinkAttribute('role','button');
                    $orderRemove->setLinkAttribute('class','btn btn-sm btn-danger btn-delete');
                    $orderRemove->setLinkAttribute('i','fa fa-trash');
                }
            }


        }
    }









}