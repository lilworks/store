<?php
namespace SiteBundle\Twig;

use Symfony\Component\HttpKernel\EventListener\ValidateRequestListener;
use Symfony\Component\Translation\TranslatorInterface;

class SiteExtension  extends \Twig_Extension
{
    private $translator;
    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }
    public function getFilters()
    {

        return array(
            new \Twig_SimpleFilter('shippingPrice', array($this, 'shippingPriceFilter')),
            new \Twig_SimpleFilter('addressInline', array($this, 'addressInlineFilter')),
            new \Twig_SimpleFilter('stock', array($this, 'stockFilter')),
            new \Twig_SimpleFilter('spec', array($this, 'specFilter')),
            new \Twig_SimpleFilter('taxes', array($this, 'taxesFilter')),
            new \Twig_SimpleFilter('warranties', array($this, 'warrantiesFilter')),
            new \Twig_SimpleFilter('totalCalculator', array($this, 'totalCalculatorFilter')),
        );
    }
    private function formatUnity($v,$unity){

        if($unity == "m"){
            $unity1 = "m";
            $unity1000 = "mm";
        }elseif($unity=='kg'){
            $unity1 = "kg";
            $unity1000 = "g";
        }

        if($v<1){
            return $v*1000 . $unity1000;
        }else{
            return $v . $unity1;
        }
    }
    public function specFilter($product)
    {
        $output = "";
        if(
            $product->getWidth() ||
            $product->getLength() ||
            $product->getHeight()  ||
            $product->getWeight()
        ){

            if($product->getWeight()){
                $output.="<li>" . $this->translator->trans('sitebundle.weigth') .": ". $this->formatUnity($product->getWeight(),'kg') ."</li>" ;
            }
            if($product->getHeight()){
                $output.="<li>" . $this->translator->trans('sitebundle.height') .": ". $this->formatUnity($product->getHeight(),'m') ."</li>" ;
            }
            if($product->getLength()){
                $output.="<li>" . $this->translator->trans('sitebundle.length') .": ". $this->formatUnity($product->getLength(),'m') ."</li>" ;
            }
            if($product->getWidth()){
                $output.="<li>" . $this->translator->trans('sitebundle.width') .": ". $this->formatUnity($product->getWidth(),'m') ."</li>" ;
            }
            return "<ul>$output</ul>";
        }else{
            return null;
        }
    }

    public function shippingPriceFilter($sm,$countryId)
    {
        return $sm->getPriceByCountry($countryId);
    }
    public function addressInlineFilter($address)
    {

       $output='';
        if($address->getName() != ''){
            $output.=$address->getName().' | ';
        }

        $output.= $address->getStreet() . ' ';

        if($address->getComplement() != ''){
            $output.= ',' . $address->getComplement().' ';
        }
        $output.=$address->getZipCode() . ' ' . $address->getCity() . ' , ' . $address->getCountry()->getName();

        return $output;
    }
    public function totalCalculatorFilter($basketProducts)
    {
        $tot=$totHt=0;
        foreach($basketProducts as $basketProduct){
            $q = $basketProduct->getQuantity();

            $sumRatioTaxes = 0;
            $sumValueTaxes = 0;
            foreach($basketProduct->getProduct()->getTaxesOnline() as $tax){
                if($tax->getType() == 'RATIO'){
                    $sumRatioTaxes+=  $tax->getValue();
                }elseif($tax->getType() == 'VALUE'){
                    $sumValueTaxes+=  $tax->getValue();
                }


            }

            $tot+=$q*$basketProduct->getProduct()->getPriceOnline();
            $totHt+= $q * 100 * ( $basketProduct->getProduct()->getPriceOnline() - $sumValueTaxes ) /  (100+$sumRatioTaxes);
        }

        return array("ttc"=>$tot,"ht"=>$totHt);
    }
    public function stockFilter($product,$quantity=-1)
    {
       $output='
       <span class="alert alert-success" role="alert">
            <i class="fa fa-cubes" aria-hidden="true"></i>'.$this->translator->trans("sitebundle.alwaysavailable").'
        </span>
        ';

        if($product->getAlwaysAvailable() == 1 ){
            $output='
               <span class="alert alert-success" role="alert">
                    '.$this->translator->trans("sitebundle.alwaysavailable").'
                </span>
                ';

        }else{
            if($quantity > $product->getStock()){
                $missing = abs($product->getStock() - $quantity);
                if($product->getLeadTime() > 0){
                    $output='<span class="alert alert-warning" role="alert"><i class="fa fa-cubes" aria-hidden="true"></i>';
                    $output.=$this->translator->transChoice(
                            '{1} missing 1 product|]1,Inf[ missing %count% products',
                            10,
                            array('%count%' => $missing)
                        ) .'
                        ';
                    $output.='|
                            '.$this->translator->transChoice(
                            '{1} available in 1 day|]1,Inf[ available in %leadTime% days',
                            10,
                            array('%leadTime%' => $product->getLeadTime())
                        ) .'

                        ';
                    $output.='</span>';
                }else{
                    $output='<span class="alert alert-danger" role="alert"><i class="fa fa-cubes" aria-hidden="true"></i>';
                    $output.='

                            '.$this->translator->transChoice(
                            '{1} missing 1 product|]1,Inf[ missing %count% products, please contact us',
                            10,
                            array('%count%' => $missing)
                        ) .'

                        ';

                    $output.='</span>';
                }
            }
        }

        return $output;
    }

    public function taxesFilter($taxes)
    {
        $output = "";
        if(count($taxes)>0){
            $output.="<ul class=\"list-group twig-taxes\">";
            foreach($taxes as $taxe){
                $output.="<li class=\"list-group-item list-group-item-info\">".$taxe->getName()."</li>";
            }
            $output.="</ul>";
        }
        return $output;
    }
    public function warrantiesFilter($warranties)
    {
        $output = "";
        if(count($warranties)>0){
            $output.="<ul class=\"list-group twig-warranties\">";
            foreach($warranties as $warrantie){
                $output.="<li class=\"list-group-item list-group-item-info\">".$warrantie->getName()."</li>";
            }
            $output.="</ul>";
        }
        return $output;
    }
}