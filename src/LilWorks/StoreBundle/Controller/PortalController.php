<?php
namespace LilWorks\StoreBundle\Controller;

use LilWorks\StoreBundle\Entity\Brand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Filter\BrandFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Portal controller.
 *
 */
class PortalController extends Controller
{

    public function userAction(Request $request)
    {


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.user'));

        return $this->render('LilWorksStoreBundle:Portal:user.html.twig', array(
        ));
    }

    public function orderAction(Request $request)
    {


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.order'));

        return $this->render('LilWorksStoreBundle:Portal:order.html.twig', array(
        ));
    }

    public function productAction(Request $request)
    {


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.product'));

        return $this->render('LilWorksStoreBundle:Portal:product.html.twig', array(
        ));
    }

    public function shippingAction(Request $request)
    {


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.shipping'));

        return $this->render('LilWorksStoreBundle:Portal:shipping.html.twig', array(
        ));
    }

    public function paymentAction(Request $request)
    {


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.payment'));

        return $this->render('LilWorksStoreBundle:Portal:payment.html.twig', array(
        ));
    }
    public function siteAction(Request $request)
    {


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.site'));

        return $this->render('LilWorksStoreBundle:Portal:site.html.twig', array(
        ));
    }

}
