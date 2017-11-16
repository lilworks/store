<?php
namespace AppBundle\Document;

use Symfony\Cmf\Bundle\SeoBundle\Extractor\TitleReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\DescriptionReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\ExtrasReadInterface;

class Product implements TitleReadInterface, DescriptionReadInterface, ExtrasReadInterface
{
    protected $title;
    protected $publishDate;
    protected $intro;

    public function getSeoTitle()
    {
        return "GET SEO TITLE";
    }

    public function getSeoDescription()
    {
        return "GET SEO DESC";
        return substr(strip_tags($this->intro),0,100);
    }

    public function getSeoExtras()
    {
        return [
            'property' => [
                'og:title'       => $this->title,
                'og:description' => $this->description,
            ],
        ];
    }
}