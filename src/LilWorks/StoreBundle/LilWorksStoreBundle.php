<?php

namespace LilWorks\StoreBundle;

use LilWorks\StoreBundle\DependencyInjection\LilWorksStoreExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LilWorksStoreBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new LilWorksStoreExtension();
    }
}
