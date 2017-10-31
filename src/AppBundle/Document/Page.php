<?php
namespace AppBundle\Document;

use Symfony\Cmf\Bundle\SeoBundle\SeoAwareInterface;

class Page implements SeoAwareInterface
{
    protected $seoMetadata;

    // ...
    public function getSeoMetadata()
    {
        die("CCCCCCCCC");
        return $this->seoMetadata;
    }

    public function setSeoMetadata($metadata)
    {
        die("CCCCCCCCC");
        $this->seoMetadata = $metadata;
    }
}