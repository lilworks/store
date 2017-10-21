<?php

namespace LilWorks\StoreBundle\Controller;

use AppBundle\Entity\User;
use LilWorks\StoreBundle\Entity\Address;
use LilWorks\StoreBundle\Entity\Customer;
use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\PhoneNumber;
use LilWorks\StoreBundle\Filter\OrderFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Import controller.
 *
 */
class ImportController extends Controller
{

    private $_bit_check = 8;    # bit amount for diff algor.
    private $_iv= "fYfhHeDm";   # 8 bit IV Attention à cette clef... si elle est perdu aucun mot de passe ne pourra etre retrouve



    public function encrypt($text,$key) {
        $text_num =str_split($text,$this->_bit_check);
        $text_num = $this->_bit_check-strlen($text_num[count($text_num)-1]);
        for ($i=0;$i<$text_num; $i++) {
            $text = $text . chr($text_num);
        }
        $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES,'','cbc','');
        mcrypt_generic_init($cipher, $key, $this->_iv);
        $decrypted = mcrypt_generic($cipher,$text);
        mcrypt_generic_deinit($cipher);
        return base64_encode($decrypted);
    }

    public function decrypt($encrypted_text,$key){
        $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES,'','cbc','');
        mcrypt_generic_init($cipher, $key, $this->_iv);
        $decrypted = mdecrypt_generic($cipher,base64_decode($encrypted_text));
        mcrypt_generic_deinit($cipher);
        $last_char=substr($decrypted,-1);
        for($i=0;$i<$this->_bit_check-1; $i++){
            if(chr($i)==$last_char){
                $decrypted=substr($decrypted,0,strlen($decrypted)-$i);
                break;
            }
        }
        return $decrypted;
    }

    public function getNames($name){

        $name = ltrim($name);
        $name = str_replace('  ',' ',$name);
        $names = explode(' ',$name);

        $first = null;
        $firstName = "";
        foreach($names as $v){
            if(!$first){
                $lastName = $v;
                $first = 1;
            }else{
                $firstName.=$v." ";
            }
        }

        return array('first'=>rtrim($firstName),'last'=>$lastName);
    }

    /**
     *
     */
    public function indexAction(Request $request)
    {

        $imported = array();
        $emImport = $this->getDoctrine()->getManager('import');
        $em = $this->getDoctrine()->getManager();
        $connection = $emImport->getConnection();

        $statement = $connection->prepare("SELECT * FROM users ");
        $statement->execute();
        $results = $statement->fetchAll();
        $max = 20;
        $i = 0;
        foreach($results as $result){
            $localUser = $em->getRepository("AppBundle:User")->findOneByEmailCanonical(strtolower($result['usr_email']));
           if(! $localUser){

               $user = new User();
               $user->setUsername($result['usr_email']);
               $user->setEmail($result['usr_email']);
               $user->setPlainPassword(
                   $this->decrypt($result['usr_password'],$result['usr_password_salt'])
               );


               $date = new \DateTime();
               $date->setTimestamp(strtotime($result['usr_dateregister']));
                $user->setPasswordRequestedAt($date);

               $user->setEnabled(1);



               $statement = $connection->prepare("SELECT * FROM clients WHERE usr_id = :id ");
               $statement->bindValue('id', $result['usr_id']);
               $statement->execute();
               $resultClient = $statement->fetchAll();

               $statement = $connection->prepare("SELECT * FROM commandes WHERE usr_id = :id ");
               $statement->bindValue('id', $result['usr_id']);
               $statement->execute();
               $resultClientCommande = $statement->fetchAll();


               if(count($resultClient)>0 && count($resultClientCommande)>0 ){

                   $em->persist($user);

                   $resultClient = $resultClient[0];
                    $customer = new Customer();
                    $customer->setUser($user);
                    if($resultClient['cli_company'] != ""){
                        $customer->setCompanyName($resultClient['cli_company']);
                    }



                    if($resultClient['cli_name'] != ""){
                        $formatedNames = $this->getNames($resultClient['cli_name']);
                        $customer->setLastName($formatedNames['first']);
                        $customer->setFirstName($formatedNames['last']);


                        $customer->setCreatedAt(
                            $date
                        );


                        $customer->setCompanyName($resultClient['cli_company']);
                    }else{
                        $customer->setFirstName('UNKNOW');
                        $customer->setLastName('UNKNOW');
                    }


                    if($resultClient['liv_adr_id']>0){
                        $statement = $connection->prepare("SELECT * FROM adresses WHERE adr_id = :id ");
                        $statement->bindValue('id', $resultClient['liv_adr_id']);
                        $statement->execute();
                        $resultAddress = $statement->fetchAll();
                        $resultAddress=$resultAddress[0];
                        $address = new Address();
                        $address->setCustomer($customer);
                        $address->setName($resultAddress["adr_name"]);
                        $address->setStreet($resultAddress["adr_adr"]);
                        $address->setComplement($resultAddress["adr_lieudit"]);
                        $address->setZipCode($resultAddress["adr_code"]);
                        $address->setCity($resultAddress["adr_ville"]);

                        $statement = $connection->prepare("SELECT * FROM pays WHERE pay_id = :id ");
                        $statement->bindValue('id', $resultAddress["pay_id"]);
                        $statement->execute();
                        $resultAddressPays = $statement->fetchAll();
                        $resultAddressPays = $resultAddressPays[0];
                        $country = $em->getRepository('LilWorksStoreBundle:Country')->findOneByTag($resultAddressPays['pay_short']);

                        $address->setCountry($country);
                        $em->persist($address);

                    }

                    if($resultClient['fac_adr_id']>0){

                        $statement = $connection->prepare("SELECT * FROM adresses WHERE adr_id = :id ");
                        $statement->bindValue('id', $resultClient['fac_adr_id']);
                        $statement->execute();
                        $resultAddress = $statement->fetchAll();

                        $resultAddress=$resultAddress[0];
                        $address = new Address();
                        $address->setCustomer($customer);
                        $address->setName($resultAddress["adr_name"]);
                        $address->setStreet($resultAddress["adr_adr"]);
                        $address->setComplement($resultAddress["adr_lieudit"]);
                        $address->setZipCode($resultAddress["adr_code"]);
                        $address->setCity($resultAddress["adr_ville"]);

                        $statement = $connection->prepare("SELECT * FROM pays WHERE pay_id = :id ");
                        $statement->bindValue('id', $resultAddress["pay_id"]);
                        $statement->execute();
                        $resultAddressPays = $statement->fetchAll();
                        $resultAddressPays = $resultAddressPays[0];
                        $country = $em->getRepository('LilWorksStoreBundle:Country')->findOneByTag($resultAddressPays['pay_short']);

                        $address->setCountry($country);

                        $em->persist($address);

                    }



                    $statement = $connection->prepare("SELECT * FROM telephones WHERE cli_id = :id ");
                    $statement->bindValue('id', $resultClient['usr_id']);
                    $statement->execute();
                    $resultPhonenumbers = $statement->fetchAll();
                    foreach($resultPhonenumbers as $resultPhonenumber){
                        $phonenumber = new PhoneNumber();
                        $phonenumber->setCustomer($customer);
                        $phonenumber->setPhonenumber($resultPhonenumber['tel_num']);
                        $em->persist($phonenumber);
                    }


                   $em->persist($customer);

                   array_push($imported,array($user,$customer));
                   $i++;
                   if($i >= $max)
                       break;
                }


           }
           # break;
        }
        $em->flush();


        return $this->render('LilWorksStoreBundle:Import:index.html.twig', array(
            'imported'=> $imported
        ));
    }



}
