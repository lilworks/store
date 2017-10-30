<?php
namespace LilWorks\StoreBundle\Service;


class TagCleaner
{
    private $em;


    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;


        return $this;
    }
    public function clean($entity,$entityFileField)
    {
        $objects = $this->em->getRepository($entity)->findAll();
        foreach($objects as $object){
            $object->setName($object->getName());
            $this->em->persist($object);
        }

        $this->em->flush();

        return $objects;
    }
}