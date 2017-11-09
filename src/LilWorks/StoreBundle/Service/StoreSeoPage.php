<?php
namespace LilWorks\StoreBundle\Service;

use Sonata\SeoBundle\Seo\SeoPage;
use Symfony\Component\Translation\TranslatorInterface;


class StoreSeoPage
{

    private $context;
    private $mode;
    private $translator;
    private $seoPage;

    public function __construct(SeoPage $seoPage,TranslatorInterface $translator,$context,$mode)
    {
        $this->context = $context;
        $this->mode = $mode;
        $this->translator = $translator;
        $this->seoPage = $seoPage;


    }

    public function setTitle( $title,$transParam=array(),$prefix = null , $prefixParam=array(),$domain = "messages" ){


        $translatedPrefix = $this->translator->trans($prefix,$prefixParam,$domain);

        $translatedTitle = $this->translator->trans($title,$transParam,$domain);
        $translatedTitle.= " | ".$this->context."•".$this->mode;

        $this->seoPage->setTitle( ($translatedPrefix)?$translatedPrefix . " - " . $translatedTitle:$translatedTitle );
    }

}
