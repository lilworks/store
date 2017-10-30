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

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.index'));

        return $this->render('LilWorksStoreBundle:Portal:index.html.twig', array(
            'date'=>new \DateTime()
        ));
    }


    public function userAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.user'));

        return $this->render('LilWorksStoreBundle:Portal:user.html.twig', array(
            "customersAll"=>$em->getRepository("LilWorksStoreBundle:Customer")->findAll(),
            "addressesAll"=>$em->getRepository("LilWorksStoreBundle:Address")->findAll(),
            "phonenumbersAll"=>$em->getRepository("LilWorksStoreBundle:Phonenumber")->findAll(),
            "usersAll"=>$em->getRepository("AppBundle:User")->findAll(),
            "sessionsAll"=>$em->getRepository("AppBundle:Session")->findAll(),
        ));
    }

    public function documentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.document'));

        return $this->render('LilWorksStoreBundle:Portal:document.html.twig', array(
            "ordersAll"=>$em->getRepository("LilWorksStoreBundle:Order")->findAll(),
            "couponsAll"=>$em->getRepository("LilWorksStoreBundle:Coupon")->findAll(),
            "depositsalesAll"=>$em->getRepository("LilWorksStoreBundle:DepositSale")->findAll(),
        ));
    }

    public function productAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.product'));

        return $this->render('LilWorksStoreBundle:Portal:product.html.twig', array(
            "productsAll"=>$em->getRepository("LilWorksStoreBundle:Product")->findAll(),
            "productsPublished"=>$em->getRepository("LilWorksStoreBundle:Product")->findByIsPublished(1),
            "productsArchived"=>$em->getRepository("LilWorksStoreBundle:Product")->findByIsArchived(1),
            "brandsAll"=>$em->getRepository("LilWorksStoreBundle:Brand")->findAll(),
            "brandsPublished"=>$em->getRepository("LilWorksStoreBundle:Brand")->findByIsPublished(1),
            "categoriesAll"=>$em->getRepository("LilWorksStoreBundle:Category")->findAll(),
            "categoriesPublished"=>$em->getRepository("LilWorksStoreBundle:Category")->findByIsPublished(1),
            "supercategoriesAll"=>$em->getRepository("LilWorksStoreBundle:SuperCategory")->findAll(),
            "supercategoriesPublished"=>$em->getRepository("LilWorksStoreBundle:SuperCategory")->findByIsPublished(1),
            "tagsAll"=>$em->getRepository("LilWorksStoreBundle:Tag")->findAll(),
            "warrantiesAll"=>$em->getRepository("LilWorksStoreBundle:Warranty")->findAll(),

        ));
    }

    public function shippingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.shipping'));

        return $this->render('LilWorksStoreBundle:Portal:shipping.html.twig', array(
            "shippingsAll"=>$em->getRepository("LilWorksStoreBundle:ShippingMethod")->findAll(),
            "countriesAll"=>$em->getRepository("LilWorksStoreBundle:Country")->findAll(),
        ));
    }

    public function paymentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.payment'));

        return $this->render('LilWorksStoreBundle:Portal:payment.html.twig', array(
            "paymentsAll"=>$em->getRepository("LilWorksStoreBundle:PaymentMethod")->findAll(),
            "taxesAll"=>$em->getRepository("LilWorksStoreBundle:Tax")->findAll(),
        ));
    }



    public function configAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $context = $this->getParameter('context');
        $mode = $this->getParameter('mode');

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.portal.config'));

        return $this->render('LilWorksStoreBundle:Portal:config.html.twig', array(
            'context'=>$context,
            'mode'=>$mode,
            "annoncesAll"=>$em->getRepository("LilWorksStoreBundle:Annonce")->findAll(),
            "annoncesPublished"=>$em->getRepository("LilWorksStoreBundle:Annonce")->findByIsPublished(1),
            "textsAll"=>$em->getRepository("LilWorksStoreBundle:Text")->findAll(),
            "subscribersAll"=>$em->getRepository("LilWorksStoreBundle:Subscriber")->findAll(),
            "conversationsAll"=>$em->getRepository("LilWorksStoreBundle:Conversation")->findAll(),
        ));
    }

}
