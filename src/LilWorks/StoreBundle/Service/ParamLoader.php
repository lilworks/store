<?php

namespace LilWorks\StoreBundle\Service;

use Symfony\Component\Form\Extension\Templating\TemplatingExtension;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class ParamLoader implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private $mode;
    private $context;
    private $currency;

    public function __construct($mode, $context,$currency, ContainerInterface $container)
    {
        $this->container = $container;
        $this->mode = $mode;
        $this->context = $context;
        $this->currency = $currency;

        $this->container->get('twig')->addGlobal('lilworks_store_mode',$mode);
        $this->container->get('twig')->addGlobal('lilworks_store_context',$context);
        $this->container->get('twig')->addGlobal('lilworks_store_currency',$currency);

    }
    public function getMode(){
        return $this->mode;
    }
    public function getBehavior(){
        return $this->behavior;
    }
    public function getCurrency(){
        return $this->currency;
    }
}