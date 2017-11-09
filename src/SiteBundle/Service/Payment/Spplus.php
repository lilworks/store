<?php
namespace SiteBundle\Service\Payment;



class Spplus  implements PaymentInterface
{

    public $dataToSend;
    public $datas;

    public function  __construct($datas,$order,$rootDir){

        $this->datas = $datas;

        $dataToSend = array();
        $dataToSend[$datas['ACTION_MODE_NAME']]=$datas['ACTION_MODE_VALUE'];
        $dataToSend[$datas['AMOUNT_NAME']]=100*$order->getTot();
        $dataToSend[$datas['ACTION_CTX_NAME']]=$datas['ACTION_CTX_VALUE'];
        $dataToSend[$datas['CURRENCY_NAME']]=$datas['CURRENCY_VALUE'];
        $dataToSend[$datas['PAGE_ACTION_NAME']]=$datas['PAGE_ACTION_VALUE'];
        $dataToSend[$datas['PAYMENT_CONFIG_NAME']]=$datas['PAYMENT_CONFIG_VALUE'];
        $dataToSend[$datas['SITE_ID_NAME']]=$datas['SITE_ID_VALUE'];
        $dataToSend[$datas['TRANS_DATE_NAME']]=date('YmdHis');
        $dataToSend[$datas['TRANS_ID_NAME']]=$this->_getTransId($rootDir);;
        $dataToSend[$datas['VERSION_NAME']]=$datas['VERSION_VALUE'];
        $dataToSend[$datas['URL_RETURN_NAME']]=$datas['URL_RETURN_VALUE'];
        $dataToSend[$datas['CUSTOMER_EMAIL_NAME']]=$order->getCustomer()->getUser()->getEmail();
        //$dataToSend[$datas['ORDER_ID_NAME']]=$order->getReference();
        $dataToSend[$datas['CERTIFICAT_NAME']]=$datas['CERTIFICAT_VALUE'];

        $vads = array();
        foreach($dataToSend as $k=>$v){
            if(strstr($k,"vads")){
                $vads[$k]=$v;
            }
        }

        $dataToSend[$datas['SIGNATURE_NAME']]=$this->_getSignature($vads,$datas['CERTIFICAT_VALUE']);
        $this->dataToSend = $dataToSend;
        return $this;
    }

    public function  getForm(){

        $form= '<form method="POST" action="'.$this->datas["ACTION_URL"].'">';

        foreach ($this->dataToSend as $nom => $valeur)
        {
            if(strstr($nom,"vads_")   || $nom == "signature"){
                $form.='<input type="hidden" name="' . $nom . '" value="' . $valeur . '" />';
            }
        }
        $form.='<button type="submit" class="validationButton" >';
        $form.=	'<span><em>PAYER => envoyer les paramètres vers la plateforme de paiement</em></span>';
        $form.='</button>';
        $form.='</form>';
        return $form;
    }

    public function getRedirection(){
        return 'https://paiement.systempay.fr/vads-payment/?'.http_build_query($this->paymentMethod->getSendParameters());
    }
    public function  getName(){
        return $this->datas["_name"];
    }

    public function  getText(){
        return $this->datas["_text"];
    }

    private function _getSignature($field,$certificat) {


        ksort($field); // tri des paramétres par ordre alphabétique

        $contenu_signature = "";
        foreach ($field as $nom => $valeur)
        {
            if(substr($nom,0,5) == 'vads_') {
                $contenu_signature .= $valeur."+";
            }
        }

        $contenu_signature .= $certificat;	// On ajoute le certificat à la fin de la chaîne.
        $signature = sha1($contenu_signature);
        return($signature);

    }

    private function _getTransId($rootDir) {
        // Dans cet exemple la valeur du compteur est stocké dans un fichier count.txt,incrémenté de 1 et remis à zéro si la valeur est superieure à 899999
        // ouverture/lock
        $filename = $rootDir . "/config/payment.txt";// il faut ici indiquer le chemin du fichier.
        $fp = fopen($filename, 'r+');
        flock($fp, LOCK_EX);

        // lecture/incrémentation
        $count = (int)fread($fp, 6);    // (int) = conversion en entier.
        $count++;
        if($count < 0 || $count > 899999) {
            $count = 0;
        }
        // on revient au début du fichier
        fseek($fp, 0);
        ftruncate($fp,0);
        // écriture/fermeture/Fin du lock
        fwrite($fp, $count);
        flock($fp, LOCK_UN);
        fclose($fp);

        // le trans_id : on rajoute des 0 au début si nécessaire
        $trans_id = sprintf("%06d",$count);
        return($trans_id);
    }
    public function getSendParameters(){
        return $this->dataToSend;
    }
}
