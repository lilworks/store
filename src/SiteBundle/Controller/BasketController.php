<?php
namespace SiteBundle\Controller;

use LilWorks\StoreBundle\Entity\BasketsRealShippingMethods;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class BasketController extends Controller
{

    public function emptyAction(Request $request)
    {
        $this->get('site.basket')->emptyBasket();

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    public function shippingMethodAction(Request $request)
    {
        $basketService =  $this->get('site.basket');
        $basket = $this->get('site.basket')->getBasket();
        $user = $this->getUser();

        if(is_null($user) || !$user->getCustomer()){
            return $this->redirectToRoute('fos_user_security_login');
        }

        $form = $this->createForm('SiteBundle\Form\BasketShippingMethodType', $basket);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach($basket->getBasketsRealShippingMethods() as $v){
                foreach($v->getBasketsProducts() as $p){
                    $p->setBasketRealShippingMethod(null);
                }
                $basket->removeBasketsRealShippingMethod($v);
                $em->remove($v);
            }


            if($form->has("basketsRealShippingMethods") && isset($basketService->shippingCalculator->combinationsBeforeOrder[$form->get("basketsRealShippingMethods")->getData()])){


                /*
                                foreach($basketService->shippingCalculator->combinationsBeforeOrder[$form->get("basketsRealShippingMethods")->getData()]["datas"] as $kcomb=>$v){
                                    var_dump($v["shippingMethod"]->getName());
                                }
                                die();
                */


                                foreach($basketService->shippingCalculator->combinationsBeforeOrder[$form->get("basketsRealShippingMethods")->getData()]["datas"] as $smToAdd){

                                    $basketRealShippingMethod = new BasketsRealShippingMethods();
                                    //$basketRealShippingMethod->setShippingMethod($em->getRepository("LilWorksStoreBundle:ShippingMethod")->find($smId));
                                    $basketRealShippingMethod->setShippingMethod($smToAdd["shippingMethod"]);
                                    $basketRealShippingMethod->setBasket($basket);
                                    $basketRealShippingMethod->setPrice($smToAdd["price"]);

                                    foreach($smToAdd["products"] as $product){
                                        $productObject = $em->getRepository("LilWorksStoreBundle:BasketsProducts")->findOneBy(array(
                                            "basket"=>$basket->getId(),
                                            "product"=>$product)
                                        );

                                        $productObject->setBasketRealShippingMethod($basketRealShippingMethod);
                                        $basketRealShippingMethod->addBasketsProduct($productObject);
                                    }
                                    $basket->addBasketsRealShippingMethod($basketRealShippingMethod);
                                }

            }



            $totals = $basketService->getTotals();


            $basket->setTot(floatval($totals["TTC"]+$totals["SM"]));

            $em->persist($basket);
            $em->flush();





            if( $form->has('order') && $form->get('order')->isClicked()){
                $order = $basketService->toOrder();
                return $this->redirectToRoute('site_order_pay',array('order_id'=>$order->getId()));
            }


        }


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('sitebundle.htmltitle.basket.shippingmethod'));

        return $this->render('SiteBundle:Basket:shipping-method.html.twig',array(
            'basket'=>$basket,
            'form'=>$form->createView(),
            'user'=>$user
        ));
    }
    public function editAction(Request $request)
    {

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("sitebundle.breadcrumb.all", "site_all");

        $breadcrumbs->addRouteItem("sitebundle.breadcrumb.basketedit" , "site_basket_edit");
        $breadcrumbs->prependRouteItem("sitebundle.breadcrumb.homepage", "site_homepage");



        $basketService =  $this->get('site.basket');
        $basket = $this->get('site.basket')->getBasket();
        $user = $this->getUser();

        $form = $this->createForm('SiteBundle\Form\BasketType', $basket);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {



            $em = $this->getDoctrine()->getManager();


            foreach($basket->getBasketsRealShippingMethods() as $v){
                foreach($v->getBasketsProducts() as $p){
                    $p->setBasketRealShippingMethod(null);
                }
                $basket->removeBasketsRealShippingMethod($v);
                $em->remove($v);
            }



            $totals = $basketService->getTotals();


            $basket->setTot(floatval($totals["TTC"]+$totals["SM"]));

            $em->persist($basket);
            $em->flush();





            if( $form->get('update')->isClicked()){
                return $this->redirectToRoute('site_basket_edit');
            }elseif( $form->get('empty')->isClicked()){
                return $this->redirectToRoute('site_basket_empty');
            }elseif( $form->get('shippingMethod')->isClicked()){
                return $this->redirectToRoute('site_basket_shippingMethod');
            }elseif( $form->get('order')->isClicked()){
                $order = $basketService->toOrder();
                return $this->redirectToRoute('site_order_pay',array('order_id'=>$order->getId()));
            }

        }


        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('sitebundle.htmltitle.basket.edit'));

        return $this->render('SiteBundle:Basket:edit.html.twig',array(
            'basket'=>$basket,
            'form'=>$form->createView(),
            'user'=>$user
        ));
    }

    public function orderAction(Request $request)
    {
        $basketService =  $this->get('site.basket');
        $basket = $this->get('site.basket')->getBasket();
        $user = $this->getUser();





        return $this->render('SiteBundle:Basket:order.html.twig',array(
            'basket'=>$basket,
            'user'=>$user
        ));
    }

    /**
     * @ParamConverter("get", class="LilWorksStoreBundle:Product", options={"id" = "id"})
     */
    public function addAction(Request $request,Product $product)
    {
        $basketService =$this->get('site.basket');
        $basketService->addProduct($product);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    /**
     * @ParamConverter("get", class="LilWorksStoreBundle:Product", options={"id" = "id"})
     */
    public function removeAction(Request $request,Product $product)
    {
        $basketService =$this->get('site.basket');
        $basketService->removeProduct($product);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    /**
     * @ParamConverter("get", class="LilWorksStoreBundle:Product", options={"id" = "id"})
     */
    public function deleteAction(Request $request,Product $product)
    {
        $basketService =$this->get('site.basket');
        $basketService->deleteProduct($product);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);

    }
}