<?php
namespace LilWorks\StoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Alef\UserBundle\Service\RoleService;

class TagSanitizerListener
{
    private $roleService;

    public function __construct(RoleService $roleService) {
        $this->roleService = $roleService;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(method_exists($entity, 'setRoleService')) {
            $entity->setRoleService($this->roleService);
        }
    }
}