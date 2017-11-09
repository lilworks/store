<?php
namespace SiteBundle\Service;

use Sonata\SeoBundle\Seo\SeoPage;
use Symfony\Component\Translation\TranslatorInterface;


class SiteSeoPage
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

        $defaultTitle =  $this->seoPage->getTitle();
        $translatedTitle = $this->translator->trans($title,$transParam,$domain);
        $this->seoPage->setTitle( $defaultTitle . " - "  . $translatedTitle );
    }


}
