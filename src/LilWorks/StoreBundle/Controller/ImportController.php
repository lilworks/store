<?php

namespace LilWorks\StoreBundle\Controller;

use AppBundle\Entity\User;
use LilWorks\StoreBundle\Entity\Address;
use LilWorks\StoreBundle\Entity\Brand;
use LilWorks\StoreBundle\Entity\Customer;
use LilWorks\StoreBundle\Entity\DepositSale;
use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\OrdersOrderSteps;
use LilWorks\StoreBundle\Entity\OrdersPaymentMethods;
use LilWorks\StoreBundle\Entity\OrdersProducts;
use LilWorks\StoreBundle\Entity\PaymentMethod;
use LilWorks\StoreBundle\Entity\PhoneNumber;
use LilWorks\StoreBundle\Entity\Picture;
use LilWorks\StoreBundle\Entity\Coupon;
use LilWorks\StoreBundle\Entity\Product;
use LilWorks\StoreBundle\Entity\SuperCategoriesCategories;
use LilWorks\StoreBundle\Entity\SuperCategory;
use LilWorks\StoreBundle\Filter\OrderFilterType;
use LilWorks\StoreBundle\Service\SuperCategories;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LilWorks\StoreBundle\Entity\Category;
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

    public function offlineClientAction(Request $request)
    {
        $emImport = $this->getDoctrine()->getManager('import');
        $em = $this->getDoctrine()->getManager();
        $connection = $emImport->getConnection();
        $statement = $connection->prepare("SELECT * FROM clients ");
        $statement->execute();

        $max = 1000;
        $i=1;
        $resultClient = $statement->fetchAll();

        foreach($resultClient as $clientOffline ) {
            $formatedNames = $this->getNames($clientOffline['cli_name']);

            $customer  = $em->getRepository("LilWorksStoreBundle:Customer")->findOneBy(array(
                'firstName'=>$formatedNames['first'],
                'lastName'=>$formatedNames['last'],
                'companyName'=>$clientOffline['cli_company'],
            ));


            if(!$customer){
                $customer = new Customer();

                if ($clientOffline['cli_company'] != "") {
                    $customer->setCompanyName($clientOffline['cli_company']);
                }


                if ($clientOffline['cli_name'] != "") {

                    $customer->setLastName($formatedNames['last']);
                    $customer->setFirstName($formatedNames['first']);

                    $date = new \DateTime();
                    $date->setTimestamp(strtotime($clientOffline['cli_created']));

                    $customer->setCreatedAt(
                        $date
                    );

                    $customer->setEmail($clientOffline['cli_email']);
                    $customer->setCompanyName($clientOffline['cli_company']);
                } else {
                    $customer->setFirstName('UNKNOW');
                    $customer->setLastName('UNKNOW');
                }


                if ($clientOffline['liv_adr_id'] > 0) {
                    $statement = $connection->prepare("SELECT * FROM adresses WHERE adr_id = :id LIMIT 1");
                    $statement->bindValue('id', $clientOffline['liv_adr_id']);
                    $statement->execute();
                    if ($resultAddress = $statement->fetch()) {
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


                }

                if ($clientOffline['fac_adr_id'] > 0) {

                    $statement = $connection->prepare("SELECT * FROM adresses WHERE adr_id = :id LIMIT 1");
                    $statement->bindValue('id', $clientOffline['fac_adr_id']);
                    $statement->execute();
                    if ($resultAddress = $statement->fetch()) {
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


                }

                $statement = $connection->prepare("SELECT * FROM telephones WHERE cli_id = :id ");
                $statement->bindValue('id', $clientOffline['cli_id']);
                $statement->execute();
                $resultPhonenumbers = $statement->fetchAll();
                foreach ($resultPhonenumbers as $resultPhonenumber) {
                    $phonenumber = new PhoneNumber();
                    $phonenumber->setCustomer($customer);
                    $phonenumber->setPhonenumber($resultPhonenumber['tel_num']);
                    $em->persist($phonenumber);
                }


                $statement = $connection->prepare("SELECT * FROM docs WHERE cli_id = :id ");
                $statement->bindValue('id', $clientOffline['cli_id']);
                $statement->execute();
                $resultDocs = $statement->fetchAll();
                foreach ($resultDocs as $doc) {


                    $date = new \DateTime();


                    if (!$doc['doc_name']) {
                        $order = new Order();
                        $order->setCreatedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                        $order->setCustomer($customer);
                        $order->setReference($date->setTimestamp(strtotime($doc['doc_date']))->format('Y') . '-?????');
                        $order->setTot($doc['doc_tot']);
                        $order->setDescription($doc['doc_desc']);
                        $order->setDescriptionInternal($doc['doc_desc_interne']);

                        foreach ($customer->getAddresses() as $address) {
                            $order->setBillingAddress();
                            $order->setShippingAddress();
                            break 1;
                        }


                        $statementPayment = $connection->prepare("SELECT * FROM docs_reglements WHERE doc_id = :id ");
                        $statementPayment->bindValue('id', $doc['doc_id']);
                        $statementPayment->execute();
                        $resultPayment = $statementPayment->fetchAll();

                        foreach ($resultPayment as $payment) {
                            if ($pm = $em->getRepository("LilWorksStoreBundle:PaymentMethod")->find($payment['pai_id'])) {
                                $opm = new OrdersPaymentMethods();
                                $opm->setAmount($payment['dre_value']);
                                $opm->setPaymentMethod($pm);
                                $opm->setOrder($order);
                                $opm->setPayedAt($date->setTimestamp(strtotime($payment['dre_date'])));
                                $pm->addOrdersPaymentMethod($opm);
                                $em->persist($opm);
                            }
                        }

                        if ($doc['dst_id'] == 1) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(9);
                        } elseif ($doc['dst_id'] == 2) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(1);
                        } elseif ($doc['dst_id'] == 3) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(6);
                        } elseif ($doc['dst_id'] == 4) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(7);
                        }
                        if ($os) {
                            $oos = new OrdersOrderSteps();
                            $oos->setOrderStep($os);
                            $oos->setOrder($order);
                            $oos->setCreatedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                            $em->persist($oos);
                        }


                        $statementProduct = $connection->prepare("SELECT * FROM docs_articles WHERE doc_id = :id ");
                        $statementProduct->bindValue('id', $doc['doc_id']);
                        $statementProduct->execute();
                        $resultProduct = $statementProduct->fetchAll();
                        foreach ($resultProduct as $product) {
                            $statementProductName = $connection->prepare("SELECT * FROM articles WHERE art_id = :id LIMIT 1");
                            $statementProductName->bindValue('id', $product['art_id']);
                            $statementProductName->execute();
                            if ($resultProductName = $statementProductName->fetch()) {
                                if ($p = $em->getRepository("LilWorksStoreBundle:Product")->findOneByName($resultProductName['art_name'])) {
                                    $orderProduct = new OrdersProducts();
                                    $orderProduct->setProduct($p);
                                    $orderProduct->setOrder($order);
                                    $orderProduct->setQuantity($product['dar_q']);
                                    $orderProduct->setName($product['dar_name']);
                                    $orderProduct->setPrice($product['dar_pu']);
                                    $orderProduct->setSerialNumber($product['dar_serial']);
                                    $orderProduct->setDestocking(1);
                                    $orderProduct->setWarrantieString($product['dar_warranty']);
                                    $p->addOrdersProduct($orderProduct);

                                    if ($product['tax_id_tva'] == 1) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(1);
                                    } elseif ($product['tax_id_tva'] == 2) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(3);
                                    } elseif ($product['tax_id_tva'] == 3) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(4);
                                    } elseif ($product['tax_id_tva'] == 4) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(2);
                                    } elseif ($product['tax_id_tva'] == 5) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(6);
                                    } elseif ($product['tax_id_tva'] == 6) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(7);
                                    }
                                    if ($tva) {
                                        $orderProduct->addTax($tva);
                                        $tva->addOrdersProduct($orderProduct);
                                    }

                                    if ($product['tax_id_eco'] == 1) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(1);
                                    } elseif ($product['tax_id_eco'] == 2) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(3);
                                    } elseif ($product['tax_id_eco'] == 3) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(4);
                                    } elseif ($product['tax_id_eco'] == 4) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(2);
                                    } elseif ($product['tax_id_eco'] == 5) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(6);
                                    } elseif ($product['tax_id_eco'] == 6) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(7);
                                    }
                                    if (isset($eco)) {
                                        $orderProduct->addTax($eco);
                                        $eco->addOrdersProduct($orderProduct);
                                    }

                                    $em->persist($orderProduct);
                                }
                            }
                        }


                        $em->persist($order);
                    } elseif (strstr($doc['doc_name'], 'FA')) {
                        $order = new Order();
                        $order->setOrderType($em->getRepository("LilWorksStoreBundle:OrderType")->find(1));
                        $order->setCreatedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                        $order->setCustomer($customer);
                        $names = explode('-', $doc['doc_name']);
                        $order->setReference($date->setTimestamp(strtotime($doc['doc_date']))->format('Y') . '-' . $names[0] . $names[1]);
                        $order->setTot($doc['doc_tot']);
                        $order->setDescription($doc['doc_desc']);
                        $order->setDescriptionInternal($doc['doc_desc_interne']);
                        foreach ($customer->getAddresses() as $address) {
                            $order->setBillingAddress();
                            $order->setShippingAddress();
                            break 1;
                        }
                        $statementPayment = $connection->prepare("SELECT * FROM docs_reglements WHERE doc_id = :id ");
                        $statementPayment->bindValue('id', $doc['doc_id']);
                        $statementPayment->execute();
                        $resultPayment = $statementPayment->fetchAll();

                        foreach ($resultPayment as $payment) {
                            if ($pm = $em->getRepository("LilWorksStoreBundle:PaymentMethod")->find($payment['pai_id'])) {
                                $opm = new OrdersPaymentMethods();
                                $opm->setAmount($payment['dre_value']);
                                $opm->setPaymentMethod($pm);
                                $opm->setOrder($order);
                                $opm->setPayedAt($date->setTimestamp(strtotime($payment['dre_date'])));
                                $pm->addOrdersPaymentMethod($opm);
                                $em->persist($opm);
                            }
                        }

                        if ($doc['dst_id'] == 1) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(9);
                        } elseif ($doc['dst_id'] == 2) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(1);
                        } elseif ($doc['dst_id'] == 3) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(6);
                        } elseif ($doc['dst_id'] == 4) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(7);
                        }
                        if ($os) {
                            $oos = new OrdersOrderSteps();
                            $oos->setOrderStep($os);
                            $oos->setOrder($order);
                            $oos->setCreatedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                            $em->persist($oos);
                        }


                        $statementProduct = $connection->prepare("SELECT * FROM docs_articles WHERE doc_id = :id ");
                        $statementProduct->bindValue('id', $doc['doc_id']);
                        $statementProduct->execute();
                        $resultProduct = $statementProduct->fetchAll();

                        foreach ($resultProduct as $product) {
                            $statementProductName = $connection->prepare("SELECT * FROM articles WHERE art_id = :id LIMIT 1");
                            $statementProductName->bindValue('id', $product['art_id']);
                            $statementProductName->execute();
                            $resultProductName = $statementProductName->fetch();
                            if ($resultProductName) {

                                if ($p = $em->getRepository("LilWorksStoreBundle:Product")->findOneByName($resultProductName['art_name'])) {
                                    $orderProduct = new OrdersProducts();
                                    $orderProduct->setProduct($p);
                                    $orderProduct->setOrder($order);
                                    $orderProduct->setQuantity($product['dar_q']);
                                    $orderProduct->setName($product['dar_name']);
                                    $orderProduct->setPrice($product['dar_pu']);
                                    $orderProduct->setSerialNumber($product['dar_serial']);
                                    $orderProduct->setDestocking(1);
                                    $orderProduct->setWarrantieString($product['dar_warranty']);
                                    $p->addOrdersProduct($orderProduct);
                                    if ($product['tax_id_tva'] == 1) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(1);
                                    } elseif ($product['tax_id_tva'] == 2) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(3);
                                    } elseif ($product['tax_id_tva'] == 3) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(4);
                                    } elseif ($product['tax_id_tva'] == 4) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(2);
                                    } elseif ($product['tax_id_tva'] == 5) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(6);
                                    } elseif ($product['tax_id_tva'] == 6) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(7);
                                    }
                                    if (isset($tva)){
                                        $orderProduct->addTax($tva);
                                        $tva->addOrdersProduct($orderProduct);
                                    }

                                    if ($product['tax_id_eco'] == 1) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(1);
                                    } elseif ($product['tax_id_eco'] == 2) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(3);
                                    } elseif ($product['tax_id_eco'] == 3) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(4);
                                    } elseif ($product['tax_id_eco'] == 4) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(2);
                                    } elseif ($product['tax_id_eco'] == 5) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(6);
                                    } elseif ($product['tax_id_eco'] == 6) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(7);
                                    }
                                    if (isset($eco)){
                                        $orderProduct->addTax($eco);
                                        $eco->addOrdersProduct($orderProduct);
                                    }

                                    $em->persist($orderProduct);
                                }
                            }
                        }


                        $em->persist($order);
                    } elseif (strstr($doc['doc_name'], 'DE')) {
                        $order = new Order();
                        $order->setOrderType($em->getRepository("LilWorksStoreBundle:OrderType")->find(2));
                        $order->setCreatedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                        $order->setCustomer($customer);
                        $names = explode('-', $doc['doc_name']);
                        $order->setReference($date->setTimestamp(strtotime($doc['doc_date']))->format('Y') . '-' . $names[0] . $names[1]);
                        $order->setTot($doc['doc_tot']);
                        $order->setDescription($doc['doc_desc']);
                        $order->setDescriptionInternal($doc['doc_desc_interne']);

                        if ($doc['dst_id'] == 1) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(9);
                        } elseif ($doc['dst_id'] == 2) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(1);
                        } elseif ($doc['dst_id'] == 3) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(6);
                        } elseif ($doc['dst_id'] == 4) {
                            $os = $em->getRepository("LilWorksStoreBundle:OrderStep")->find(7);
                        }
                        if ($os) {
                            $oos = new OrdersOrderSteps();
                            $oos->setOrderStep($os);
                            $oos->setOrder($order);
                            $oos->setCreatedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                            $em->persist($oos);
                        }


                        $statementProduct = $connection->prepare("SELECT * FROM docs_articles WHERE doc_id = :id ");
                        $statementProduct->bindValue('id', $doc['doc_id']);
                        $statementProduct->execute();
                        $resultProduct = $statementProduct->fetchAll();
                        foreach ($resultProduct as $product) {
                            $statementProductName = $connection->prepare("SELECT * FROM articles WHERE art_id = :id LIMIT 1");
                            $statementProductName->bindValue('id', $product['art_id']);
                            $statementProductName->execute();
                            if ($resultProductName = $statementProductName->fetch()) {
                                if ($p = $em->getRepository("LilWorksStoreBundle:Product")->findOneByName($resultProductName['art_name'])) {
                                    $orderProduct = new OrdersProducts();
                                    $orderProduct->setProduct($p);
                                    $orderProduct->setOrder($order);
                                    $orderProduct->setQuantity($product['dar_q']);
                                    $orderProduct->setName($product['dar_name']);
                                    $orderProduct->setPrice($product['dar_pu']);
                                    $orderProduct->setSerialNumber($product['dar_serial']);
                                    $orderProduct->setDescription(1);
                                    $orderProduct->setWarrantieString($product['dar_warranty']);
                                    $p->addOrdersProduct($orderProduct);
                                    if ($product['tax_id_tva'] == 1) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(1);
                                    } elseif ($product['tax_id_tva'] == 2) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(3);
                                    } elseif ($product['tax_id_tva'] == 3) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(4);
                                    } elseif ($product['tax_id_tva'] == 4) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(2);
                                    } elseif ($product['tax_id_tva'] == 5) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(6);
                                    } elseif ($product['tax_id_tva'] == 6) {
                                        $tva = $em->getRepository("LilWorksStoreBundle:Tax")->find(7);
                                    }
                                    if (isset($tva)){
                                        $orderProduct->addTax($tva);
                                        $tva->addOrdersProduct($orderProduct);
                                    }

                                    if ($product['tax_id_eco'] == 1) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(1);
                                    } elseif ($product['tax_id_eco'] == 2) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(3);
                                    } elseif ($product['tax_id_eco'] == 3) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(4);
                                    } elseif ($product['tax_id_eco'] == 4) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(2);
                                    } elseif ($product['tax_id_eco'] == 5) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(6);
                                    } elseif ($product['tax_id_eco'] == 6) {
                                        $eco = $em->getRepository("LilWorksStoreBundle:Tax")->find(7);
                                    }
                                    if ($eco) {
                                        $orderProduct->addTax($eco);
                                        $eco->addOrdersProduct($orderProduct);
                                    }

                                    $em->persist($orderProduct);
                                }
                            }
                        }


                        $em->persist($order);
                    } elseif (strstr($doc['doc_name'], 'AV')) {
                        $coupon = new Coupon();
                        $coupon->setCreatedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                        $coupon->setCustomer($customer);
                        $names = explode('-', $doc['doc_name']);
                        $coupon->setReference($date->setTimestamp(strtotime($doc['doc_date']))->format('Y') . '-' . $names[0] . $names[1]);
                        $coupon->setAmount($doc['doc_tot']);
                        $coupon->setDescription($doc['doc_desc']);
                        $coupon->setDescriptionInternal($doc['doc_desc_interne']);
                        foreach ($customer->getAddresses() as $address) {
                            $coupon->setAddress($address);
                            break 1;
                        }
                        $em->persist($coupon);
                    } elseif (strstr($doc['doc_name'], 'DV')) {
                        $depositSale = new DepositSale();
                        $depositSale->setCreatedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                        $depositSale->setDeposedAt($date->setTimestamp(strtotime($doc['doc_date'])));
                        $depositSale->setCustomer($customer);
                        $depositSale->setPriceBuying($doc['doc_tot']);
                        $names = explode('-', $doc['doc_name']);
                        $depositSale->setReference($date->setTimestamp(strtotime($doc['doc_date']))->format('Y') . '-' . $names[0] . $names[1]);
                        $depositSale->setPriceBuying($doc['doc_name']);
                        $depositSale->setDescription($doc['doc_desc']);
                        $depositSale->setDescriptionInternal($doc['doc_desc_interne']);
                        foreach ($customer->getAddresses() as $address) {
                            $depositSale->setAddress($address);
                            break 1;
                        }
                        $em->persist($depositSale);
                    }
                }


                $em->persist($customer);
                $i++;
                if ($i > $max)
                    break;
            }
        }



        $em->flush();

        $paymentMethods = $em->getRepository("LilWorksStoreBundle:OrdersPaymentMethods")->findBy(array(
            'paymentMethod'=>5
            ));

        foreach($paymentMethods as $paymentMethod){
            $statementPm = $connection->prepare("
                  SELECT
                    dr.dre_value,
                    d2.doc_name,
                    d2.doc_date
                  FROM docs d
                  LEFT JOIN docs_reglements dr ON dr.doc_id = d.doc_id
                  LEFT JOIN docs d2 ON d2.doc_id = dr.doc_id_target
                  WHERE d.doc_name = :docname
                  AND dr.doc_id_target IS NOT NULL
                  AND YEAR(d.doc_date) = :year
              ");
            $names =explode('-',$paymentMethod->getOrder()->getReference()) ;


            $statementPm->bindValue('docname', str_replace('FA','FA-',$names[1]));
            $statementPm->bindValue('year', $names[0]);
            $statementPm->execute();
            $resultPm = $statementPm->fetchAll();

            foreach($resultPm as $v){

                $dateForYear = explode('-',$v['doc_date']);
                $reference = str_replace('-','',$v['doc_name']);

                $reference = $dateForYear[0].'-'.$reference;

                $coupon = $em->getRepository("LilWorksStoreBundle:Coupon")->findOneByReference($reference);
                $paymentMethod->setCoupon($coupon);
                $coupon->addOrdersPaymentMethod($paymentMethod);
                $em->persist($coupon);
                $em->persist($paymentMethod);

            }
        }
        $em->flush();
        return $this->render('LilWorksStoreBundle:Import:offline.html.twig', array(
        ));
    }
    /**
     *
     */

    public function testAction(){
        $emImport = $this->getDoctrine()->getManager('import');
        $em = $this->getDoctrine()->getManager();
        $connection = $emImport->getConnection();
        $statement = $connection->prepare("
            SELECT a.*,ac.*,am.*,apOnline.pri_value as priceOnline,apOffline.pri_value as priceOffline FROM articles a
            LEFT JOIN articles_categories ac ON ac.aca_id = a.aca_id
            LEFT JOIN articles_marques am ON am.ama_id = a.ama_id
            LEFT JOIN articles_prix apOnline ON apOnline.art_id = a.art_id AND apOnline.pty_id = 2
            LEFT JOIN articles_prix apOffline ON apOffline.art_id = a.art_id AND apOffline.pty_id = 3

        ");
        $statement->execute();
        $results = $statement->fetchAll();
        foreach($results as $result) {
           var_dump($result['art_isoccas']);
        }
        die();
    }
    public function offlineProductAction(Request $request){
        $emImport = $this->getDoctrine()->getManager('import');
        $em = $this->getDoctrine()->getManager();
        $connection = $emImport->getConnection();

        $statement = $connection->prepare("SELECT * FROM users ");
        $statement->execute();
        $results = $statement->fetchAll();
        $max = 20;
        $i = 0;

        foreach($results as $result) {
            $localUser = $em->getRepository("AppBundle:User")->findOneByEmailCanonical(strtolower($result['usr_email']));
            if (!$localUser) {
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
                $em->persist($user);
                $em->flush();
            }
        }

        $statement = $connection->prepare("SELECT * FROM articles_marques ");
        $statement->execute();
        $results = $statement->fetchAll();
        foreach($results as $result) {
            $brand = $em->getRepository('LilWorksStoreBundle:Brand')->findOneByName($result['ama_name']);
            if(!$brand) {
                $brand = new Brand();
                $brand->setIsPublished($result['ama_published']);
                $brand->setName($result['ama_name']);
                $brand->setPictureName($result['ama_ico']);
                $brand->setDescription($result['ama_desc']);
                $em->persist($brand);
            }
        }

        $statement = $connection->prepare("SELECT * FROM articles_categories ");
        $statement->execute();
        $results = $statement->fetchAll();
        foreach($results as $result) {
            $category = $em->getRepository('LilWorksStoreBundle:Category')->findOneByName($result['aca_name']);
            if(!$category){
                $category = new Category();
                $category->setIsPublished($result['aca_published']);
                $category->setName($result['aca_name']);
                $category->setPictureName($result['aca_ico']);
                $category->setDescription($result['aca_desc']);
               $em->persist($category);
            }
        }


        $statement = $connection->prepare("SELECT * FROM boutique_subcats ");
        $statement->execute();
        $results = $statement->fetchAll();
        foreach($results as $result) {
            $supercategory = $em->getRepository('LilWorksStoreBundle:SuperCategory')->findOneByName($result['sub_name']);
            if(!$supercategory){
                $supercategory = new SuperCategory();
                $supercategory->setName($result['sub_name']);
                $supercategory->setPictureName($result['sub_ico']);
                $supercategory->setPos($result['sub_pos']);
                $supercategory->setIsPublished(1);
                $em->persist($supercategory);
            }
        }

        $em->flush();

        $statementSupercatCat = $connection->prepare("SELECT * FROM boutique_subcats_categories");
        $statementSupercatCat->execute();
        foreach ($statementSupercatCat->fetchAll() as $supercatCat){

            $statementSupercat = $connection->prepare("SELECT * FROM boutique_subcats WHERE sub_id = :id LIMIT 1");
            $statementSupercat->bindValue('id', $supercatCat['sub_id']);
            $statementSupercat->execute();
            $rSub = $statementSupercat->fetch();



            $statementCat = $connection->prepare("SELECT * FROM articles_categories WHERE aca_id = :id LIMIT 1");
            $statementCat->bindValue('id', $supercatCat['aca_id']);
            $statementCat->execute();
            $rCat = $statementCat->fetch();

            if( $rSub && $rCat ){
                $supercategory = $em->getRepository('LilWorksStoreBundle:SuperCategory')->findOneByName($rSub['sub_name']);
                $category = $em->getRepository('LilWorksStoreBundle:Category')->findOneByName($rCat['aca_name']);
                if($supercategory && $category){
                    $superCategoryCategory = new SuperCategoriesCategories();
                    $superCategoryCategory->setCategory($category);
                    $superCategoryCategory->setSupercategory($supercategory);
                    $category->addSupercategoriesCategory($superCategoryCategory);
                    $em->persist($category);
                    $em->persist($superCategoryCategory);
                }
            }

        }
        $em->flush();


        $statement = $connection->prepare("
            SELECT
                    a.*,
                    ac.*,
                    am.*,

                    apBuying.pri_value AS priceBuying,
                    apRetail.pri_value AS priceRetail,

                    apOnline.pri_value AS priceOnline,
                    tOnlineRatio.tax_value AS taxOnlineRatio,
                    tOnlineValue.tax_value AS taxOnlineValue,

                    apOffline.pri_value AS priceOffline,
                    tOfflineRatio.tax_value AS taxOfflineRatio,
                    tOfflineValue.tax_value AS taxOfflineValue

                FROM
                    articles a
                        LEFT JOIN
                    articles_categories ac ON ac.aca_id = a.aca_id
                        LEFT JOIN
                    articles_marques am ON am.ama_id = a.ama_id

                        LEFT JOIN
                    articles_prix apBuying ON apBuying.art_id = a.art_id AND apBuying.pty_id = 1
                        LEFT JOIN
                    articles_prix apRetail ON apRetail.art_id = a.art_id AND apRetail.pty_id = 4

                        LEFT JOIN
                    articles_prix apOnline ON apOnline.art_id = a.art_id AND apOnline.pty_id = 2
                        LEFT JOIN
                    taxes tOnlineRatio ON apOnline.tax_id_tva = tOnlineRatio.tax_id
                        LEFT JOIN
                    taxes tOnlineValue ON apOnline.tax_id_eco = tOnlineValue.tax_id

                        LEFT JOIN
                    articles_prix apOffline ON apOffline.art_id = a.art_id AND apOffline.pty_id = 3
                        LEFT JOIN
                    taxes tOfflineRatio ON apOffline.tax_id_tva = tOfflineRatio.tax_id
                        LEFT JOIN
                    taxes tOfflineValue ON apOffline.tax_id_eco = tOfflineValue.tax_id

                    GROUP BY a.art_id
        ");

        $statement->execute();
        $results = $statement->fetchAll();
        foreach($results as $result) {
            $product = $em->getRepository('LilWorksStoreBundle:Product')->findOneByName($result['art_name']);
            if(!$product) {
                $product = new Product();
                $product->setName($result['art_name']);
                $product->setStock($result['art_stock']);
                $product->setDescription($result['art_desc']);
                $product->setDescriptionInternal($result['art_desc_interne']);
                $product->setIsArchived($result['art_archived']);

                $product->setIsPublished($result['art_published']);
                $product->setIsSecondHand($result['art_isoccas'] );


                // brand
                if($brand = $em->getRepository('LilWorksStoreBundle:Brand')->findOneByName($result['ama_name'])){
                    $product->setBrand($brand);
                    $brand->addProduct($product);
                }
                if($category = $em->getRepository('LilWorksStoreBundle:Category')->findOneByName($result['aca_name'])){
                    $product->addCategory($category);
                    $category->addProduct($product);
                }




                $product->setPriceBuying($result['priceBuying']);
                $product->setPriceRetail($result['priceRetail']);



                $product->setPriceOffline($result['priceOffline']);
                if($result['taxOfflineRatio'] > 0){

                    if($tax = $em->getRepository('LilWorksStoreBundle:Tax')->findOneByValue($result['taxOfflineRatio'])){
                        $product->addTaxOffline($tax);
                        $tax->addProductsOffline($product);
                    }
                }
                if($result['taxOfflineValue'] > 0){
                    if($tax = $em->getRepository('LilWorksStoreBundle:Tax')->findOneByValue($result['taxOfflineValue'])) {
                        $product->addTaxOffline($tax);
                        $tax->addProductsOffline($product);
                    }
                }


                $product->setPriceOnline($result['priceOnline']);
                if($result['taxOnlineRatio'] > 0){
                   if( $tax = $em->getRepository('LilWorksStoreBundle:Tax')->findOneByValue($result['taxOnlineRatio'])){
                        $product->addTaxOnline($tax);
                       $tax->addProductsOnline($product);
                   }
                }
                if($result['taxOnlineValue'] > 0){
                    if($tax = $em->getRepository('LilWorksStoreBundle:Tax')->findOneByValue($result['taxOnlineValue'])){
                        $product->addTaxOnline($tax);
                        $tax->addProductsOnline($product);
                    }
                }


                $statementPicture = $connection->prepare("
                    SELECT * FROM articles_illustrations WHERE art_id = :id

                ");
                $statementPicture->bindValue('id', $result['art_id']);
                $statementPicture->execute();

                foreach ($statementPicture->fetchAll() as $pictureFromOffline ){
                    $picture = new Picture();
                    $picture->setPos($pictureFromOffline['ail_pos']);
                    $picture->setPictureName($pictureFromOffline['ail_file']);
                    $picture->setDescription($pictureFromOffline['ail_desc']);
                    $picture->setProduct($product);
                    $em->persist($picture);
                }


                $em->persist($product);
            }
        }

        $em->flush();
        return $this->render('LilWorksStoreBundle:Import:offline.html.twig', array(
        ));
    }



    public function onlineClientAction(){
        #$max = 100;
        #$i=0;
        $emImport = $this->getDoctrine()->getManager('import');
        $em = $this->getDoctrine()->getManager();
        $connection = $emImport->getConnection();

        $statementUsers = $connection->prepare("SELECT * FROM users ");
        $statementUsers->execute();
        $resultsUser = $statementUsers->fetchAll();



        foreach($resultsUser as $resultUser){

            $user = $em->getRepository("AppBundle:User")->findOneByEmailCanonical(strtolower(strtolower($resultUser['usr_email'])));

            if(!$user) {
                $user = new User();
            }

            $user->setUsername($resultUser['usr_email']);
            $user->setEmail($resultUser['usr_email']);
            $user->setPlainPassword(
                $this->decrypt($resultUser['usr_password'], $resultUser['usr_password_salt'])
            );

            $date = new \DateTime();
            $date->setTimestamp(strtotime($resultUser['usr_dateregister']));
            $user->setPasswordRequestedAt($date);

            $user->setEnabled(1);
            $em->persist($user);

            $statementClient = $connection->prepare("SELECT * FROM clients c WHERE c.usr_id=:usr_id LIMIT 1;");
            $statementClient->bindValue('usr_id', $resultUser['usr_id']);
            $statementClient->execute();
            $resultClient = $statementClient->fetch();

            if($resultClient){
                $formatedNames = $this->getNames($resultClient['cli_name']);
                $customer = $em->getRepository("LilWorksStoreBundle:Customer")->findOneBy(array(
                    'email'=>$resultUser['usr_email']
                ));
                if(!$customer){
                    $customer = new Customer();
                }
                $customer->setEmail($resultUser['usr_email']);

                $date = new \DateTime();
                $date->setTimestamp(strtotime($resultUser['usr_dateregister']));
                $customer->setCreatedAt($date);

                $customer->setUser($user);
                if($resultClient['cli_company'] != ""){
                    $customer->setCompanyName($resultClient['cli_company']);
                }

                //if($resultClient['cli_name'] != ""){
                $formatedNames = $this->getNames($resultClient['cli_name']);
                $customer->setLastName($formatedNames['last']);
                $customer->setFirstName($formatedNames['first']);
                $customer->setCreatedAt(
                    $date
                );
                $customer->setCompanyName($resultClient['cli_company']);
                //}


                if($resultClient['liv_adr_id']>0){
                    $statement = $connection->prepare("SELECT * FROM adresses WHERE adr_id = :id ");
                    $statement->bindValue('id', $resultClient['liv_adr_id']);
                    $statement->execute();
                    $resultAddress = $statement->fetchAll();
                    $resultAddress=$resultAddress[0];
                    $addressLiv = new Address();
                    $addressLiv->setCustomer($customer);
                    $addressLiv->setName($resultAddress["adr_name"]);
                    $addressLiv->setStreet($resultAddress["adr_adr"]);
                    $addressLiv->setComplement($resultAddress["adr_lieudit"]);
                    $addressLiv->setZipCode($resultAddress["adr_code"]);
                    $addressLiv->setCity($resultAddress["adr_ville"]);

                    $statement = $connection->prepare("SELECT * FROM pays WHERE pay_id = :id ");
                    $statement->bindValue('id', $resultAddress["pay_id"]);
                    $statement->execute();
                    $resultAddressPays = $statement->fetchAll();
                    $resultAddressPays = $resultAddressPays[0];
                    $country = $em->getRepository('LilWorksStoreBundle:Country')->findOneByTag($resultAddressPays['pay_short']);

                    $addressLiv->setCountry($country);
                    $em->persist($addressLiv);

                }

                if($resultClient['fac_adr_id']>0){

                    $statement = $connection->prepare("SELECT * FROM adresses WHERE adr_id = :id ");
                    $statement->bindValue('id', $resultClient['fac_adr_id']);
                    $statement->execute();
                    $resultAddress = $statement->fetchAll();

                    $resultAddress=$resultAddress[0];
                    $addressFac = new Address();
                    $addressFac->setCustomer($customer);
                    $addressFac->setName($resultAddress["adr_name"]);
                    $addressFac->setStreet($resultAddress["adr_adr"]);
                    $addressFac->setComplement($resultAddress["adr_lieudit"]);
                    $addressFac->setZipCode($resultAddress["adr_code"]);
                    $addressFac->setCity($resultAddress["adr_ville"]);

                    $statement = $connection->prepare("SELECT * FROM pays WHERE pay_id = :id ");
                    $statement->bindValue('id', $resultAddress["pay_id"]);
                    $statement->execute();
                    $resultAddressPays = $statement->fetchAll();
                    $resultAddressPays = $resultAddressPays[0];
                    $country = $em->getRepository('LilWorksStoreBundle:Country')->findOneByTag($resultAddressPays['pay_short']);

                    $addressFac->setCountry($country);

                    $em->persist($addressFac);

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
            }


            $statementCommandes = $connection->prepare("SELECT * FROM commandes co WHERE co.usr_id=:usr_id");
            $statementCommandes->bindValue('usr_id', $resultUser['usr_id']);
            $statementCommandes->execute();
            $resultsCommande = $statementCommandes->fetchAll();
            // only fai
            $fai = $em->getRepository("LilWorksStoreBundle:OrderType")->find(3);

            if(count($resultsCommande)>0){
                foreach($resultsCommande as $resultCommande){

                    $order = $em->getRepository("LilWorksStoreBundle:Order")->findOneByReference($resultCommande['com_ref']);

                    if(!$order){
                        $order = new Order();

                        if(isset($addressFac)){
                            $order->setBillingAddress($addressFac);
                        }
                        if(isset($addressLiv)){
                            $order->setBillingAddress($addressLiv);
                        }

                        $order->setReference($resultCommande['com_ref']);
                        $order->setOrderType($fai);
                        $order->setCustomer($customer);

                        $date = new \DateTime();
                        $date->setTimestamp(strtotime($resultCommande['com_date']));
                        $order->setCreatedAt($date);

                        if($resultCommande['com_date_update']){
                            $date = new \DateTime();
                            $date->setTimestamp(strtotime($resultCommande['com_date_update']));
                            $order->setUpdatedAt($date);
                        }
                        $order->setDescription($resultCommande['com_desc']);
                        $order->setDescriptionInternal($resultCommande['com_desc_interne']);
                        $order->setTot($resultCommande['com_tot']);

                        $statementArticles = $connection->prepare("SELECT * FROM commandes_articles ca WHERE ca.com_ref = :com_ref");
                        $statementArticles->bindValue('com_ref', $resultCommande['com_ref']);
                        $statementArticles->execute();
                        $resultArticles = $statementArticles->fetchAll();
                        foreach($resultArticles as $article){
                            $orderProduct = new OrdersProducts();
                            $orderProduct->setOrder($order);
                            $orderProduct->setQuantity($article["car_q"]);
                            $orderProduct->setName($article["car_article"]);
                            $orderProduct->setPrice($article["car_pu"]);

                            if($article["car_eco"]){
                                if($tax = $em->getRepository('LilWorksStoreBundle:Tax')->findOneByValue($article["car_eco"])){
                                    $orderProduct->addTax($tax);
                                    $tax->addOrdersProduct($orderProduct);
                                    $em->persist($tax);
                                }
                            }
                            if($article["car_tva"]){
                                if($tax = $em->getRepository('LilWorksStoreBundle:Tax')->findOneByValue($article["car_tva"])){
                                    $orderProduct->addTax($tax);
                                    $tax->addOrdersProduct($orderProduct);
                                    $em->persist($tax);
                                }
                            }

                            $em->persist($orderProduct);
                        }


                        $spplus = $em->getRepository('LilWorksStoreBundle:PaymentMethod')->find(4);
                        $che = $em->getRepository('LilWorksStoreBundle:PaymentMethod')->find(2);

                        $statementPaiements = $connection->prepare("SELECT * FROM commandes_paiements cp WHERE cp.com_ref = :com_ref");
                        $statementPaiements->bindValue('com_ref', $resultCommande['com_ref']);
                        $statementPaiements->execute();
                        $resultPaiements = $statementPaiements->fetchAll();

                        foreach($resultPaiements as $paiement){
                            $orderPaymentMethod = new OrdersPaymentMethods();
                            $orderPaymentMethod->setOrder($order);
                            $orderPaymentMethod->setPaymentMethod(($resultCommande['com_moyen'] == "CHE")?$che:$spplus);
                            $orderPaymentMethod->setAmount($paiement['cpa_value']);
                            $date = new \DateTime();
                            $date->setTimestamp(strtotime($paiement['cpa_date']));
                            $orderPaymentMethod->setPayedAt($date);
                            $em->persist($orderPaymentMethod);
                        }


                        if($resultCommande['cst_id']) {

                            $orderOrderStep = new OrdersOrderSteps();
                            $orderOrderStep->setOrder($order);
                            $date = new \DateTime();

                            if($resultCommande['com_date_update']){
                                $date->setTimestamp(strtotime($resultCommande['com_date_update']));
                            }else{
                                $date->setTimestamp(strtotime($resultCommande['com_date']));
                            }
                            $orderOrderStep->setCreatedAt($date);

                            if ($resultCommande['cst_id'] == 1) {
                                $orderOrderStep->setOrderStep(
                                    $em->getRepository('LilWorksStoreBundle:OrderStep')->find(1)
                                );
                            } elseif ($resultCommande['cst_id'] == 2) {
                                $orderOrderStep->setOrderStep(
                                    $em->getRepository('LilWorksStoreBundle:OrderStep')->find(4)
                                );
                            } elseif ($resultCommande['cst_id'] == 3) {
                                $orderOrderStep->setOrderStep(
                                    $em->getRepository('LilWorksStoreBundle:OrderStep')->find(5)
                                );
                            } elseif ($resultCommande['cst_id'] == 4) {
                                $orderOrderStep->setOrderStep(
                                    $em->getRepository('LilWorksStoreBundle:OrderStep')->find(6)
                                );
                            } elseif ($resultCommande['cst_id'] == 5) {
                                $orderOrderStep->setOrderStep(
                                    $em->getRepository('LilWorksStoreBundle:OrderStep')->find(7)
                                );
                            } elseif ($resultCommande['cst_id'] == 6 || $resultCommande['cst_id'] == 7) {
                                $orderOrderStep->setOrderStep(
                                    $em->getRepository('LilWorksStoreBundle:OrderStep')->find(3)
                                );
                            } elseif ($resultCommande['cst_id'] == 8) {
                                $orderOrderStep->setOrderStep(
                                    $em->getRepository('LilWorksStoreBundle:OrderStep')->find(7)
                                );
                            }
                            $em->persist($orderOrderStep);
                        }


                        $customer->addOrder($order);

                        $em->persist($customer);
                        $em->persist($order);
                    }
                }
            }




            #if( $i>=$max )
            #    break;
            #$i++;

            $em->flush();
        }


        return $this->render('LilWorksStoreBundle:Import:online.html.twig', array());


    }



    /*
    public function onlineProductAction(Request $request)
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
*/


}
