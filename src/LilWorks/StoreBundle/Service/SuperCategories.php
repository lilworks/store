<?php
namespace LilWorks\StoreBundle\Service;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;


class SuperCategories implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $em;
    protected $templating;


    public function __construct(\Doctrine\ORM\EntityManager $em ,  $templating , ContainerInterface $container )
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->container = $container;

    }
    public function init()
    {
        return $this;
    }

    public function getMenu(){
        $repository = $this->em->getRepository('LilWorksStoreBundle:SuperCategory');
        $supercategories = $repository->findBy(
            array('isPublished'=>1),
            array('pos'=>'asc')
        );
        return $supercategories;
    }

}