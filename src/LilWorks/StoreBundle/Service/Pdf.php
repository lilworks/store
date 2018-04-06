<?php
namespace LilWorks\StoreBundle\Service;


use LilWorks\StoreBundle\Entity\Text;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Pdf implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $em;
    protected $templating;
    protected $header;
    protected $footer;
    protected $content;
    protected $basePath;
    protected $rootDir;

    public function __construct(\Doctrine\ORM\EntityManager $em ,  $templating , ContainerInterface $container )
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->container = $container;


        $this->header = "";
        $this->footer = "";
        $this->content = "";


        $this->basePath =  $this->container->get('request_stack')->getCurrentRequest()->getBasePath();
        $this->rootDir =  $this->container->get('kernel')->getProjectDir();



    }
    public function getResponse(){
        $pdf = $this->container->get('knp_snappy.pdf');
        $pdf->setOption('footer-html', $this->footer);
        $pdf->setOption('footer-left', "[page]/[topage]");
        $pdf->setOption('header-html', $this->header);



        return $pdf->getOutputFromHtml($this->content);

    }

    public function setContent($vars, $template = null){
        (!$template)?$template='LilWorksStoreBundle:Pdf:content.html.twig':null;
        $this->content = $this->templating->render($template,$vars);
        $this->content = str_replace('/ajaxupload/', $this->rootDir . '/web/ajaxupload/',$this->content);
    }

    public function setHeader($vars, $template = null){
        (!$template)?$template='LilWorksStoreBundle:Pdf:header.html.twig':null;
        $this->header = $this->templating->render($template, $vars);
        $this->header = str_replace('/ajaxupload/', $this->rootDir . '/web/ajaxupload/',$this->header);
    }

    public function setFooter($vars, $template = null){
        (!$template)?$template='LilWorksStoreBundle:Pdf:footer.html.twig':null;
        $this->footer = $this->templating->render($template, $vars);
        $this->footer = str_replace('/ajaxupload/',  $this->rootDir . '/web/ajaxupload/',$this->footer);
    }

}