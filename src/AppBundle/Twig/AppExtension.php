<?php
namespace AppBundle\Twig;


use Symfony\Component\HttpKernel\EventListener\ValidateRequestListener;



class AppExtension  extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('truncatehtml', array($this, 'truncatehtmlFilter')),
            new \Twig_SimpleFilter('formataddress', array($this, 'formataddressFilter')),
            new \Twig_SimpleFilter('boolean', array($this, 'booleanFilter')),
            new \Twig_SimpleFilter('idinkey', array($this, 'idinkeyFilter')),
            new \Twig_SimpleFilter('secondsToTime', array($this, 'secondsToTimeFilter')),
            new \Twig_SimpleFilter('linkbtn', array($this, 'linkbtnFilter')),
        );
    }


    public function linkbtnFilter($contain,$routes)
    {
        $btns = "";
        $i = 1;
        foreach($routes as $route){

            $icon = '<i class="fa fa-'.$route['icon'].'" aria-hidden="true"></i>';
            if($i < count($routes)){
                $btns.= '<a role="button" href="'.$route['route'].'" class="btn btn-sm btn-secondary">'.$icon.'</a>';
            }else{
                $btns.= '<a role="button" href="'.$route['route'].'" class="btn btn-sm btn-secondary">'.$icon.' '.$contain.'</a>';
            }
            $i++;
        }

        return '<div class="btn-group " role="group">'.$btns.'</div>';
    }
    public function booleanFilter($boolean,$nullOption=null)
    {
        if($boolean == 1){
            return '<span style="color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>';
        }elseif($boolean == 0){
            return '<span style="color: red;"><i class="fa fa-remove" aria-hidden="true"></i></span>';
        }elseif(is_null($boolean)){
            if(!$nullOption)
                return '<i class="fa fa-question-circle-o" aria-hidden="true"></i>';
            elseif($nullOption == 1)
                return '<span style="color: green;"><i class="fa fa-check" aria-hidden="true"></i></span>';
            elseif($nullOption == 0)
                return '<span style="color: red;"><i class="fa fa-remove" aria-hidden="true"></i></span>';

        }

    }
    function secondsToTimeFilter($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    }
    public function truncatehtmlFilter($html, $limit, $endchar = '&hellip;')
    {
        $noHtml = strip_tags($html);
        return substr($noHtml,0,$limit);

    }
    public function idinkeyFilter($results)
    {
        $output = array();
        foreach($results as $result){
            $output[$result->getId()] = $result;
        }
        return $output;

    }
    public function formataddressFilter($address) {

    if(is_null($address)){
        return "";
    }

        if($address->getName() && $address->getName() != ""){
            $name = "<span>".$address->getName()."</span>";
        }else{
            $name = "";
        }

        if($address->getStreet() && $address->getStreet() != ""){
            $street = "<p>".$address->getStreet()."</p>";
        }else{
            $street = "";
        }

        if($address->getComplement() && $address->getComplement() != ""){
            $complement = "<p>".$address->getComplement()."</p>";
        }else{
            $complement = "";
        }
        if(
            $address->getZipCode() && $address->getZipCode() != "" &&
            $address->getCity() && $address->getCity() != "" &&
            $address->getCountry() && $address->getCountry()->getName() != ""
        ){
            $zipCode = "<p>".$address->getZipCode()." ".$address->getCity().", ".$address->getCountry()->getName()."</p>";
        }else{
            $zipCode = "";
            $city = "";
            $country = "";
        }


        return "
            $name
            $street
            $complement
            $zipCode
            ";
    }
}