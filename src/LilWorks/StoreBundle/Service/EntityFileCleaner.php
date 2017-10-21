<?php
namespace LilWorks\StoreBundle\Service;

use Symfony\Component\Finder\Finder;

class EntityFileCleaner
{
    private $em;
    private $root_dir;

    public function __construct(\Doctrine\ORM\EntityManager $em, $root_dir)
    {
        $this->em = $em;
        $this->root_dir = $root_dir;

        return $this;
    }
    public function clean($web_dir,$entity,$entityFileField)
    {

        $folder = $this->root_dir."/../web/".$web_dir;

        $finder = new Finder();
        $finder->files()->in($folder);

        $removedFiles = array();
        $findFunction = "findOneBy".ucfirst($entityFileField);
        foreach ($finder as $file) {
            if(!$this->em->getRepository($entity)->$findFunction($file->getRelativePathname())){
                $filename = $file->getRelativePathname();
                unlink($file->getRealPath());
                array_push($removedFiles,$filename);
            }
        }

        return $removedFiles;
    }
}