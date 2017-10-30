<?php
namespace LilWorks\StoreBundle\Twig;

use Symfony\Component\HttpKernel\EventListener\ValidateRequestListener;
use Symfony\Component\Translation\TranslatorInterface;



class ProductsExtension  extends \Twig_Extension
{
    private $translator;

    public function __construct(TranslatorInterface $translator){
        $this->translator = $translator;
    }
    public function getFilters()
    {

        return array(
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
            new \Twig_SimpleFilter('totPriceCalculator', array($this, 'totPriceCalculator')),
            new \Twig_SimpleFilter('priceCalculator', array($this, 'priceCalculator')),
            new \Twig_SimpleFilter('displayStock', array($this, 'displayStockFilter')),
        );
    }
    public function displayStockFilter($product,$format = "badge",$icon=true,$text=null)
    {


        $contents = [
            'alwaysavailable'=>['text'=>$this->translator->trans('storebundle.product.alwaysavailable'),'icon'=>'<i class="fa fa-cubes"></i>'],
            'contactus'=>['text'=>$this->translator->trans('storebundle.product.notavailable.contactus'),'icon'=>'<i class="fa fa-cubes"></i><i class="fa fa-phone"></i>'],
            'leadtime'=>['text'=>$this->translator->trans('storebundle.product.notavailable.leadtime %leadtime%',array('%leadtime%'=>$product->getLeadTime())),'icon'=>'<i class="fa fa-cubes"></i>'],
            'available'=>['text'=>$this->translator->trans('storebundle.product.available'),'icon'=>'<i class="fa fa-cubes"></i>'],
        ];
        if( is_null($text) )
            foreach($contents as $k=>$content)
                $contents[$k]['text'] = "&nbsp;";
        if( is_null($icon) )
            foreach($contents as $k=>$content)
                $contents[$k]['icon'] = "";



        if($product->getAlwaysAvailable()){
            $message = '<span class="badge badge-success">'.$contents['alwaysavailable']['icon'].$contents['alwaysavailable']['text'].'</span>';
            $basketColor = 'green;';
        }else{

            if($product->getStock() == 0){
                if($product->getLeadTime()==0){
                    $message = '<span class="badge badge-danger">'.$contents['contactus']['icon'].$contents['contactus']['text'].'</span>';
                    $basketColor = 'red;';
                }else{
                    $message = '<span class="badge badge-warning">'.$contents['leadtime']['icon'].$contents['leadtime']['text'].'</span>';
                    $basketColor = 'orange;';
                }
            }else{
                $message = '<span class="badge badge-success">'.$contents['available']['icon'].$contents['available']['text'].'</span>';
                $basketColor = 'green;';
            }

        }
        return [
            'message'=>$message,
            'basketColor'=>$basketColor
        ];

    }
    public function priceFilter($number,$type="", $decimals = 2, $decPoint = ',', $thousandsSep = '')
    {

        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        if($type == "TTC" || $type == "HT")
            $price = $price."€ <sup>" . $type."</sup>";
        else
            $price = $price."€";
        return $price;
    }
    public function totPriceCalculator($tot,$products)
    {
        $totHt=0;
        foreach($products as $product){
            $sumRatioTaxes = 0;
            $sumValueTaxes = 0;
            foreach($product->getTaxes() as $tax){
                if($tax->getType() == 'RATIO'){
                    $sumRatioTaxes+=  $tax->getValue();
                }elseif($tax->getType() == 'VALUE'){
                    $sumValueTaxes+=  $tax->getValue();
                }


            }
                $totHt+= $product->getQuantity() * 100 * ( $product->getPrice() - $sumValueTaxes ) /  (100+$sumRatioTaxes);

        }
        //$totHt = number_format($totHt, 2,',','') . "€ HT";
        //$tot = number_format($tot, 2,',',''). "€ TTC";
        return array("HT"=>$totHt,"TTC"=>$tot);

    }
    public function priceCalculator($price,$product)
    {
        $tot = $product->getQuantity() * $price;

        $totHt=0;

            $sumRatioTaxes = 0;
            $sumValueTaxes = 0;
            foreach($product->getTaxes() as $tax){
                if($tax->getType() == 'RATIO'){
                    $sumRatioTaxes+=  $tax->getValue();
                }elseif($tax->getType() == 'VALUE'){
                    $sumValueTaxes+=  $tax->getValue();
                }

            }
            $totHt+= $product->getQuantity() * 100 * ( $product->getPrice() - $sumValueTaxes ) /  (100+$sumRatioTaxes);


        //$totHt = number_format($totHt, 2,',','') . "€ HT";
        //$tot = number_format($tot, 2,',',''). "€ TTC";

        return array("HT"=>$totHt,"TTC"=>$tot);
    }
}