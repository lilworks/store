<?php
namespace SiteBundle\Controller;

use LilWorks\StoreBundle\Entity\OrdersOrderSteps;
use LilWorks\StoreBundle\Entity\OrdersProducts;
use LilWorks\StoreBundle\Entity\OrderStep;
use Proxies\__CG__\LilWorks\StoreBundle\Entity\BasketsRealShippingMethods;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use LilWorks\StoreBundle\Entity\Product;
use LilWorks\StoreBundle\Entity\Order;
use SiteBundle\Form\BasketType;

class BasketController extends Controller
{


    public function editAction(Request $request)
    {

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

            if($form->has("basketsRealShippingMethods") && isset($basketService->shippingCalculator->combinations[$form->get("basketsRealShippingMethods")->getData()])){
                foreach($basketService->shippingCalculator->combinations[$form->get("basketsRealShippingMethods")->getData()]["datas"] as $smId=>$smToAdd){
                    $basketRealShippingMethod = new BasketsRealShippingMethods();
                    $basketRealShippingMethod->setShippingMethod($em->getRepository("LilWorksStoreBundle:ShippingMethod")->find($smId));
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

            if( $form->get('update')->isClicked())
                return $this->redirectToRoute('site_basket_edit');
            if( $form->get('order')->isClicked()){
                $totals = $basketService->getTotals();
                //$order = $em->getRepository("LilWorksStoreBundle:Basket")->basketToOrder($basket,floatval($totals["TTC"]+$totals["SM"]));
                $order = $basketService->toOrder();
                return $this->redirectToRoute('site_order_pay',array('id'=>$order->getId()));

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
        $em =  $this->get('doctrine');
        $session = $request->getSession();
        $session->start();


        $currentRoute = $request->get('_route');

        $basket = $em->getRepository('LilWorksStoreBundle:Basket')->findOneByToken($session->getId());


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
        $em =  $this->get('doctrine');
        $session = $request->getSession();
        $session->start();


        $currentRoute = $request->get('_route');

        $basket = $em->getRepository('LilWorksStoreBundle:Basket')->findOneByToken($session->getId());


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
        $em =  $this->get('doctrine');
        $session = $request->getSession();
        $session->start();


        $currentRoute = $request->get('_route');

        $basket = $em->getRepository('LilWorksStoreBundle:Basket')->findOneByToken($session->getId());


        $basketService->deleteProduct($product);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);

    }
}