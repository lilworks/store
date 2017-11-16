<?php
namespace SiteBundle\Controller;

use LilWorks\StoreBundle\Entity\Text;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TextController extends Controller
{


    public function contentAction(Text $text)
    {

        return $this->render('SiteBundle:Text:content.html.twig',array(
            'text'=>$text
        ));
    }

        public function footerAction()
        {

                $text = $this->getDoctrine()->getRepository('LilWorksStoreBundle:Text')->findOneBy(
                    array(
                        'tag'=>'footer'
                    )
                );

            return $this->render('SiteBundle:Text:footer.html.twig',array(
                'text'=>$text->getContent()
            ));
        }
    public function TextAction($tag)
    {

        $text = $this->getDoctrine()->getRepository('LilWorksStoreBundle:Text')->findOneBy(
            array(
                'tag'=>$tag
            )
        );


        return $this->render('SiteBundle:Text:text.html.twig',array(
            'text'=>$text->getContent()
        ));
    }
}