<?php
namespace AppBundle\Twig;

use Symfony\Component\HttpKernel\EventListener\ValidateRequestListener;

class DevExtension  extends \Twig_Extension
{

    private $environment;


    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('die', array($this,'killRender')),
        );
    }


    public function killRender($message = null)
    {
        if ('dev' === $this->environment) {
            die($message);
        }

        return '';
    }
}