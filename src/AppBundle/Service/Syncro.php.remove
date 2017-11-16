<?php
namespace AppBundle\Service;

use AppBundle\Entity\User;
use Database\Query\Grammars\MySqlGrammar;
use LilWorks\StoreBundle\Entity\Address;
use LilWorks\StoreBundle\Entity\Country;
use LilWorks\StoreBundle\Entity\Customer;
use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\OrdersOrderSteps;
use LilWorks\StoreBundle\Entity\OrdersPaymentMethods;
use LilWorks\StoreBundle\Entity\OrdersProducts;
use LilWorks\StoreBundle\Entity\PhoneNumber;
use LilWorks\StoreBundle\Entity\OrdersRealShippingMethods;
use LilWorks\StoreBundle\Entity\RemoteUser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Response;
use \DbSync\DbSync;
use DbSync\Transfer\Transfer;
use \DbSync\Hash\ShaHash;
use \DbSync\Table;
use \DbSync\ColumnConfiguration;
use PDO;
use Database\Connection;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
//use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Loader\DelegatingLoader;
use LilWorks\StoreBundle\Loader\YamlSyncroLoader;


class Syncro
{

    protected $emLocal;
    protected $emRemote;
    protected $emDistant;
    protected $config;


    public function __construct(\Doctrine\ORM\EntityManager $emLocale,\Doctrine\ORM\EntityManager $emRemote,\Doctrine\ORM\EntityManager $emDistant){

        $this->emLocal = $emLocale;
        $this->emRemote = $emRemote;
        $this->emDistant = $emDistant;

        $configDirectories = array("/Users/lil-works1/Webdisk/www/symfony-master/storeOffline/src/LilWorks/StoreBundle/Resources/config/parameters/syncro.yml");
        $locator = new FileLocator($configDirectories);
        $loaderResolver = new LoaderResolver(array(new YamlSyncroLoader($locator)));
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $this->config = $delegatingLoader->load("/Users/lil-works1/Webdisk/www/symfony-master/storeOffline/src/LilWorks/StoreBundle/Resources/config/parameters/syncro.yml");

        return $this;

    }


    public function test(){

        $query = $this->emRemote->createQuery(
            'SELECT o
            FROM LilWorksStoreBundle:Order o
            LEFT JOIN  LilWorksStoreBundle:OrdersOrderSteps oos WITH oos.order = o.id
            LEFT JOIN  LilWorksStoreBundle:OrderStep os WITH os.id = oos.orderStep
            WHERE o.reference like :ref and os.tag = :os
            '
        )
            ->setParameter('ref', "%inter%")
            ->setParameter('os', "DONE")
        ;
        $remoteOrders = $query->getResult();
        foreach($remoteOrders as $order){
            $remoteCustomer = $order->getCustomer();

            $onlineCustomer = $this->emLocal->getRepository("LilWorksStoreBundle:Customer")->findOneBy(array("remoteUser"=>$remoteCustomer->getUser()->getId()));
            if(!$onlineCustomer){
                $onlineCustomer = new Customer();
            }

            foreach($remoteCustomer->getPhonenumbers() as $phonenumber){
                $onlinePhonenumber = clone $phonenumber;
                $onlinePhonenumber->setCustomer($onlineCustomer);
                $onlineCustomer->addPhonenumber($onlinePhonenumber);
                $this->emLocal->persist($onlinePhonenumber);
            }
            foreach($remoteCustomer->getAddresses() as $address){
                $onlineAddress = clone $address;
                $onlineAddress->setCustomer($onlineCustomer);
                $onlineCustomer->addAddress($onlineAddress);
                $this->emLocal->persist($onlineAddress);
            }
            foreach($remoteCustomer->getOrders() as $order){
                $onlineOrder = clone $order;
                $order->setCustomer($onlineCustomer);
                $onlineCustomer->addOrder($order);
                $this->emLocal->persist($order);
            }

            $onlineCustomer->setName($remoteCustomer->getName());
            $onlineCustomer->setRemoteUser($remoteCustomer->getUser()->getId());
            $this->emLocal->persist($onlineCustomer);

        }
        $this->emLocal->flush($onlineCustomer);

    }




    public function checkIsAnUpdate($result){
        // user just change is email or is customerName or both so is customer id remain the same
        $userLocal = $this->emLocal->getRepository("AppBundle:User")->find($result["id"]);

        if($userLocal && $userLocal->getCustomer()->getId() == $result["customerId"]){
            return true;
        }
        return false;
    }


    public function getUserDataFromRemote($id){
        $connectionRemote = $this->getConnection('remote');
        $statementUser = $connectionRemote->prepare("SELECT * FROM storeRemote.fos_user where id=:id");
        $statementUser->bindValue('id', $id);
        $statementUser->execute();
        $results = $statementUser->fetchAll();
        return $results[0];
    }

    /*
     * @param LilWorksStoreBundle:Customer $from
     * @param LilWorksStoreBundle:Customer $to
     * @return null
     */
    public function setCustomerOrders($from,$to){
        $remoteOrdersShippingMethods = array();
        foreach ($from->getOrders() as $order) {
            $newOrder = new Order();

            $newOrder->setCustomer($to);
            $newOrder->setTot($order->getTot());
            $newOrder->setPayed($order->getPayed());
            $newOrder->setCreatedAt($order->getCreatedAt());
            $newOrder->setReference($order->getReference());

            $newOrder->setBillingAddressString($order->getBillingAddressString());
            $newOrder->setShippingAddressString($order->getShippingAddressString());

            foreach($order->getOrdersRealShippingMethods() as $orderRealShippingMethod){
                $newOrderShippingMethod = new OrdersRealShippingMethods();
                $newOrderShippingMethod->setOrder($newOrder);
                $newOrderShippingMethod->setPrice($orderRealShippingMethod->getPrice());
                $newOrderShippingMethod->setReference($orderRealShippingMethod->getReference());

                $this->emLocal->persist($newOrderShippingMethod);
                $this->emLocal->flush();

                $remoteOrdersShippingMethods[$orderRealShippingMethod] = $newOrderShippingMethod->getId();

                $newOrder->addOrdersRealShippingMethod($newOrderShippingMethod);
            }
            foreach($order->getOrdersPaymentMethods() as $orderPaymentMethod){
                $newOrderPaymentMethod = new OrdersPaymentMethods();
                $newOrderPaymentMethod->setPayedAt($orderPaymentMethod->getPayedAt());
                $newOrderPaymentMethod->setAmount($orderPaymentMethod->getAmount());
                $newOrderPaymentMethod->setPaymentMethod($orderPaymentMethod->getPaymentMethod());
                $newOrderPaymentMethod->setOrder($newOrder);
                $newOrder->addOrdersPaymentMethod($newOrderPaymentMethod);
                $this->emLocal->persist($newOrderPaymentMethod);
            }

            foreach($order->getOrdersOrderSteps() as $orderOrderStep){
                $newOrderOrderStep = new OrdersOrderSteps();
                $newOrderOrderStep->setCreatedAt($orderOrderStep->getCreatedAt());
                $newOrderOrderStep->setOrder($newOrder);
                $newOrderOrderStep->setDescription($orderOrderStep->getDescription());
                $newOrderOrderStep->setOrderStep($orderOrderStep->getOrderStep());

                $newOrder->addOrdersOrderStep($newOrderOrderStep);
            }

            foreach($order->getOrdersProducts() as $orderProduct){
                $newOrderProduct = new OrdersProducts();
                $newOrderProduct->setOrder($newOrder);
                $newOrderProduct->setPrice($orderProduct->getPrice());
                $newOrderProduct->setName($orderProduct->getNAme());
                $newOrderProduct->setIsSecondHand($orderProduct->getIsSecondHand());
                $newOrderProduct->setDescription($orderProduct->getDescription());
                $newOrderProduct->setSerialNumber($orderProduct->getSerialNumber());
                $newOrderProduct->setWarrantieString($orderProduct->getWarrantieString());

                $newOrderShippingMethod = $this->emLocal->getRepository("LilWorksStoreBundle:OrdersRealShippingMethods")->find($remoteOrdersShippingMethods[$orderProduct->getOrderRealShippingMethod()]);
                if($newOrderShippingMethod)
                    $newOrderProduct->setOrderRealShippingMethod($newOrderShippingMethod);


                foreach($orderProduct->getTaxes() as $tax){
                    $newOrderProduct->addTax($tax);
                }
                foreach($orderProduct->getWarranties() as $warranty){
                    $newOrderProduct->addWarranty($warranty);
                }

                $newOrder->addOrdersProduct($newOrderProduct);
            }

            $this->emLocal->persist($newOrder);
            $this->emLocal->persist($to);

        }
    }
    /*
     * @param LilWorksStoreBundle:Customer $from
     * @param LilWorksStoreBundle:Customer $to
     * @return null
     */
    public function setCustomerAdditional($from,$to){
        // First remove all existing additional
        foreach ($to->getPhonenumbers() as $phonenumber) {
            $to->removePhonenumber($phonenumber);
            $this->emLocal->remove($phonenumber);
        }
        foreach ($to->getAddresses() as $address) {
            $to->removeAddress($phonenumber);
            $this->emLocal->remove($address);
        }
        // After Enter all additional
        foreach ($from->getPhonenumbers() as $phonenumber) {
            $newPhonenumber = new PhoneNumber();
            $newPhonenumber->setPhonenumber($phonenumber->getPhonenumber());
            $newPhonenumber->setDescription($phonenumber->getDescription());
            $newPhonenumber->setCustomer($to);
            $to->addPhonenumber($newPhonenumber);
            $this->emLocal->persist($newPhonenumber);
        }

        foreach ($from->getAddresses() as $address) {
            $newAddress = new Address();
            $newAddress->setCity($address->getCity());
            $newAddress->setZipCode($address->getZipCode());
            $newAddress->setStreet($address->getStreet());
            $newAddress->setCompanyName($address->getCompanyName());
            $newAddress->setComplement($address->getComplement());
            $newAddress->setName($address->getName());
            $country = $this->emLocal->getRepository("LilWorksStoreBundle:Country")->find($address->getCountry()->getId());
            $newAddress->setCountry($country);
            $newAddress->setCustomer($to);
            $to->addAddress($newAddress);
            $this->emLocal->persist($newAddress);
        }

        $this->emLocal->persist($to);
        $this->emLocal->flush();

    }
    public function create($customerId){
        echo "TRY TO CREATE CUSTOMER ID = $customerId";
        $oCustomerRemote = $this->emRemote->getRepository("LilWorksStoreBundle:Customer")->find($customerId);

        $userDataRemote = $this->getUserDataFromRemote($oCustomerRemote->getUser()->getId());

        $newUser = new User();
        $newUser->setEmail($userDataRemote["email"]);
        $newUser->setPassword($userDataRemote["password"]);
        $newUser->setEnabled($userDataRemote["enabled"]);
        $newUser->setUsername($userDataRemote["username"]);
        $newUser->setRoles(unserialize($userDataRemote["roles"]));
        $newUser->setConfirmationToken($userDataRemote["password_requested_at"]);
        $newUser->setLastLogin(new \DateTime($userDataRemote["last_login"]));
        $newUser->setSalt($userDataRemote["salt"]);

        $this->emLocal->persist($newUser);
        $this->emLocal->flush();

        $newCustomer = new Customer();
        $newCustomer->setName($oCustomerRemote->getName());
        $newCustomer->setUser($newUser);
        $newUser->setCustomer($newCustomer);

        $this->emLocal->persist($newCustomer);
        $this->emLocal->flush();

        $this->setCustomerAdditional($oCustomerRemote,$newCustomer);
        $this->setCustomerOrders($oCustomerRemote,$newCustomer);

        $this->removeInRemote($oCustomerRemote);
    }



    public function update($customerId){
        echo "TRY TO UPDATE CUSTOMER ID = $customerId";
        $localCustomer = $this->emLocal->getRepository("LilWorksStoreBundle:Customer")->find($customerId);
        $remoteCustomer = $this->emRemote->getRepository("LilWorksStoreBundle:Customer")->find($customerId);
        $remoteUserData = $this->getUserDataFromRemote($remoteCustomer->getUser()->getId());

        $localCustomer->setName($remoteCustomer->getName());
        $localUser = $localCustomer->getUser();
        $localUser->setEmail($remoteUserData["email"]);
        $localUser->setEnabled($remoteUserData["enabled"]);
        $localUser->setUsername($remoteUserData["username"]);
        $localUser->setPassword($remoteUserData["password"]);
        $localUser->setSalt($remoteUserData["salt"]);
        $localUser->setRoles(unserialize($remoteUserData["roles"]));

        $this->emLocal->persist($localCustomer);
        $this->emLocal->persist($localUser);
        $this->setCustomerAdditional($remoteCustomer,$localCustomer);
        $this->setCustomerOrders($remoteCustomer,$localCustomer);

        $this->emLocal->flush();
        $this->removeInRemote($remoteCustomer);
    }


    public function removeInRemote($oCustomerRemote){
        echo "TRY TO REMOVE CUSTOMER AND USER IN REMOTE DATABASE";
        $remoteUserId = $oCustomerRemote->getUser()->getId();
        $connectionRemote = $this->getConnection('remote');
        $statementDetachUser = $connectionRemote->prepare("UPDATE lilworks_customer SET user = NULL WHERE id = :id;");
        $statementDetachUser->bindValue('id', $oCustomerRemote->getId());
        $statementDetachUser->execute();
        $this->emRemote->remove($oCustomerRemote);

        $statementRemoveUser = $connectionRemote->prepare("DELETE from fos_user WHERE id = :id;");
        $statementRemoveUser->bindValue('id', $remoteUserId);
        $statementRemoveUser->execute();
    }

    public function getConnection($which = 'local'){
        $s = "em".ucfirst($which);
        return $this->$s->getConnection();
    }

    public function dumpDistantToRemote(){
        $cmd = $this->config['parameters']['cmd']['dumpDistantToRemote']   .  " " .
            $this->config['parameters']['source']['host']        .  " " .
            $this->config['parameters']['source']['port']        .  " " .
            $this->config['parameters']['source']['username']    .  " " .
            $this->config['parameters']['source']['password']    .  " " .
            $this->config['parameters']['source']['dbname']      .  " " .
            $this->config['parameters']['target']['username']    .  " " .
            $this->config['parameters']['target']['password']    .  " " .
            $this->config['parameters']['target']['dbname'];
        echo $cmd;
        $process = new Process($cmd);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return true;
    }

    public function dumpLocalToDistant(){
        $process = new Process($this->config['parameters']['cmd']['dumpLocalToDistant']);
        $process->run();
        /*
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }*/
        return true;
    }
    /*
     * Il ne reste maintenant que des utilisateurs déjà enregistrés pour lesquels il faut:
     * Ecraser les address et phonenumbers
     * Ajouter les nouveaux Order
     *
     */
    public function getChangesOnExistant(){
        $connectionLocal = $this->getConnection();
        $statement = $connectionLocal->prepare(
            "SELECT id,customer,phonenumber,description,isFrom FROM
              (
                  SELECT id,customer,phonenumber,description,'remote' as isFrom FROM storeRemote.lilworks_phonenumber
                    UNION ALL
                  SELECT id,customer,phonenumber,description,'local' as isFrom FROM storeLocal.lilworks_phonenumber
              ) r1
              GROUP BY id,customer,phonenumber,description
              HAVING COUNT(*) = 1 and isFrom = 'remote'
              "
        );
        $statement->execute();
        $results = $statement->fetchAll();
        foreach($results as $result){
            $remotePhonenumber = $this->emRemote->getRepository("LilWorksStoreBundle:PhoneNumber")->find($result["id"]);
            $localPhonenumber = $this->emLocal->getRepository("LilWorksStoreBundle:PhoneNumber")->find($result["id"]);
            if($localPhonenumber){
                $phonenumber = $localPhonenumber;
            }else{
                $phonenumber = new PhoneNumber();
            }
            $phonenumber->setPhonenumber($result["phonenumber"]);
            $phonenumber->setDescription($result["description"]);
            $this->emLocal->persist($phonenumber);
        }

        $statement = $connectionLocal->prepare(
            "SELECT id,country,name,companyName,number,street,complement,zipCode,city,description,isFrom FROM
              (
                  SELECT id,country,name,companyName,number,street,complement,zipCode,city,description,'remote' as isFrom FROM storeRemote.lilworks_address
                    UNION ALL
                  SELECT id,country,name,companyName,number,street,complement,zipCode,city,description,'local' as isFrom FROM storeLocal.lilworks_address
              ) r1
              GROUP BY id,country,name,companyName,number,street,complement,zipCode,city,description
              HAVING COUNT(*) = 1 and isFrom = 'remote'
              "
        );
        $statement->execute();
        $results = $statement->fetchAll();
        foreach($results as $result){
            $remoteAddress = $this->emRemote->getRepository("LilWorksStoreBundle:Address")->find($result["id"]);
            $localAddress = $this->emLocal->getRepository("LilWorksStoreBundle:Address")->find($result["id"]);
            if(!$localAddress){
                $localAddress = new Address();
            }
            $localAddress->setName($remoteAddress->getName());
            $localAddress->setCompanyName($remoteAddress->getCompanyName());
            $localAddress->setNumber($remoteAddress->getNumber());
            $localAddress->setStreet($remoteAddress->getStreet());
            $localAddress->setComplement($remoteAddress->getComplement());
            $localAddress->setZipCode($remoteAddress->getZipCode());
            $localAddress->setCity($remoteAddress->getCity());
            $localAddress->setDescription($remoteAddress->getDescription());
            $this->emLocal->persist($localAddress);
        }

        $statement = $connectionLocal->prepare(
            "SELECT id,updatedAt,userComment,payed,isFrom FROM
              (
                  SELECT id,updatedAt,userComment,payed,'remote' as isFrom FROM storeRemote.lilworks_order
                    UNION ALL
                  SELECT id,updatedAt,userComment,payed,'local' as isFrom FROM storeLocal.lilworks_order
              ) r1
              GROUP BY id,updatedAt,userComment,payed
              HAVING COUNT(*) = 1 and isFrom = 'remote'
              "
        );
        $statement->execute();
        $results = $statement->fetchAll();
        foreach($results as $result){
            $remoteOrder = $this->emRemote->getRepository("LilWorksStoreBundle:Order")->find($result["id"]);
            $localOrder = $this->emLocal->getRepository("LilWorksStoreBundle:Order")->find($result["id"]);
            if(!$localOrder){
                $localOrder = new Order();
                $localOrder->setCustomer($remoteOrder->getCustomer());
                $localOrder->setTot($remoteOrder->getTot());
                $localOrder->setPayed($remoteOrder->getPayed());
                $localOrder->setCreatedAt($remoteOrder->getCreatedAt());
                $localOrder->setReference($remoteOrder->getReference());

                $localOrder->setBillingAddressString($remoteOrder->getBillingAddressString());
                $localOrder->setShippingAddressString($remoteOrder->getShippingAddressString());

                $remoteOrdersShippingMethods = array();
                foreach($remoteOrder->getOrdersRealShippingMethods() as $orderRealShippingMethod){
                    $newOrderShippingMethod = new OrdersRealShippingMethods();
                    $newOrderShippingMethod->setOrder($localOrder);
                    $newOrderShippingMethod->setPrice($orderRealShippingMethod->getPrice());
                    $newOrderShippingMethod->setReference($orderRealShippingMethod->getReference());

                    $this->emLocal->persist($newOrderShippingMethod);
                    $this->emLocal->flush();

                    $remoteOrdersShippingMethods[$orderRealShippingMethod] = $newOrderShippingMethod->getId();

                    $localOrder->addOrdersRealShippingMethod($newOrderShippingMethod);
                }
                foreach($remoteOrder->getOrdersPaymentMethods() as $orderPaymentMethod){
                    $newOrderPaymentMethod = new OrdersPaymentMethods();
                    $newOrderPaymentMethod->setPayedAt($orderPaymentMethod->getPayedAt());
                    $newOrderPaymentMethod->setAmount($orderPaymentMethod->getAmount());
                    $newOrderPaymentMethod->setPaymentMethod($orderPaymentMethod->getPaymentMethod());
                    $newOrderPaymentMethod->setOrder($localOrder);
                    $localOrder->addOrdersPaymentMethod($newOrderPaymentMethod);
                    $this->emLocal->persist($newOrderPaymentMethod);
                }

                foreach($remoteOrder->getOrdersOrderSteps() as $orderOrderStep){
                    $newOrderOrderStep = new OrdersOrderSteps();
                    $newOrderOrderStep->setCreatedAt($orderOrderStep->getCreatedAt());
                    $newOrderOrderStep->setOrder($localOrder);
                    $newOrderOrderStep->setDescription($orderOrderStep->getDescription());
                    $newOrderOrderStep->setOrderStep($orderOrderStep->getOrderStep());

                    $localOrder->addOrdersOrderStep($newOrderOrderStep);
                }

                foreach($remoteOrder->getOrdersProducts() as $orderProduct){
                    $newOrderProduct = new OrdersProducts();
                    $newOrderProduct->setOrder($localOrder);
                    $newOrderProduct->setPrice($orderProduct->getPrice());
                    $newOrderProduct->setName($orderProduct->getNAme());
                    $newOrderProduct->setIsSecondHand($orderProduct->getIsSecondHand());
                    $newOrderProduct->setDescription($orderProduct->getDescription());
                    $newOrderProduct->setSerialNumber($orderProduct->getSerialNumber());
                    $newOrderProduct->setWarrantieString($orderProduct->getWarrantieString());

                    $newOrderShippingMethod = $this->emLocal->getRepository("LilWorksStoreBundle:OrdersRealShippingMethods")->find($remoteOrdersShippingMethods[$orderProduct->getOrderRealShippingMethod()]);
                    if($newOrderShippingMethod)
                        $newOrderProduct->setOrderRealShippingMethod($newOrderShippingMethod);


                    foreach($orderProduct->getTaxes() as $tax){
                        $newOrderProduct->addTax($tax);
                    }
                    foreach($orderProduct->getWarranties() as $warranty){
                        $newOrderProduct->addWarranty($warranty);
                    }

                    $localOrder->addOrdersProduct($newOrderProduct);
                }
            }
            $localOrder->setUserComment($remoteOrder->getUserComment());
            $localOrder->setUpdatedAt($remoteOrder->getUpdatedAt());
        }

        $this->emLocal->flush();
    }
    public function getNewRemoteOrders(){

        $this->dumpDistantToRemote();

        $connectionLocal = $this->getConnection();

        $statementRemoteUserCustomer = $connectionLocal->prepare(
            "SELECT id ,email,isFrom,cname , customerId  FROM
              (
                  SELECT fu.id ,fu.email,'remote' as isFrom, lc.name as cname , lc.id as customerId  FROM storeRemote.fos_user fu
                  LEFT JOIN storeRemote.lilworks_customer lc ON lc.user = fu.id
                  UNION ALL
                  SELECT fu.id,fu.email,'local' as isFrom, lc.name as cname , lc.id as customerId   FROM storeLocal.fos_user fu
                  LEFT JOIN storeLocal.lilworks_customer lc ON lc.user = fu.id
              ) r1
              GROUP BY id,email,cname ,customerId
              HAVING COUNT(*) = 1 and isFrom = 'remote'
              "
        );

        $statementRemoteUserCustomer->execute();
        $resultsUserCustomerRemote = $statementRemoteUserCustomer->fetchAll();



        // Je dois savoir si c'est un nouveau user ou un update
        foreach($resultsUserCustomerRemote as  $userCustomerRemote){
            $isAnUpdate = $this->checkIsAnUpdate($userCustomerRemote);
            if($isAnUpdate){
                $this->update($userCustomerRemote["customerId"]);
            }else{
                $this->create($userCustomerRemote["customerId"]);
            }
        }




        $this->getChangesOnExistant();

        $this->dumpLocalToDistant();



        die();

/*
        $connectionLocal = $this->emLocal->getConnection();
        $connectionRemote = $this->emRemote->getConnection();

        $statementCustomer = $connectionLocal->prepare(
            "SELECT id,name,user,isFrom FROM
              (
              SELECT id,name,user,'remote' as isFrom FROM storeRemote.lilworks_customer
              UNION ALL
              SELECT id,name,user,'local' as isFrom FROM storeLocal.lilworks_customer
              ) r1
              GROUP BY id,name,user
              HAVING COUNT(*) = 1 and isFrom = 'remote'
              "
        );
        //$statement->bindValue('id', 9999);
        $statementCustomer->execute();
        $resultsRemoteCustomer = $statementCustomer->fetchAll();

        $remoteUsers = array();
        $newLocalUsers = array();
        $remoteCustomers = array();
        $newLocalCusomers = array();
        $remotePhonenumbers = array();
        $remoteAddresses = array();
        $remoteOrders = array();
        $remoteOrdersShippingMethods = array();

        foreach($resultsRemoteCustomer as $remoteCustomer){

            $oCustomerRemote = $this->emRemote->getRepository("LilWorksStoreBundle:Customer")->find($remoteCustomer["id"]);

            $statement = $connectionRemote->prepare("SELECT * FROM fos_user WHERE id = :id ");
            $statement->bindValue('id', $oCustomerRemote->getUser()->getId());
            $statement->execute();
            $results = $statement->fetchAll();


            $newUser = new User();
            $newUser->setEmail($results[0]["email"]);
            $newUser->setPassword($results[0]["password"]);
            $newUser->setEnabled($results[0]["enabled"]);
            $newUser->setUsername($results[0]["username"]);
            $newUser->setRoles(unserialize($results[0]["roles"]));
            $newUser->setLastLogin($results[0]["last_login"]);
            $newUser->setConfirmationToken($results[0]["password_requested_at"]);
            $newUser->setSalt($results[0]["salt"]);

            $this->emLocal->persist($newUser);
            $this->emLocal->flush();

            array_push($newLocalUsers ,$newUser->getId() );

            array_push($remoteUsers,$results[0]["id"]);

            $newCustomer = new Customer();
            $newCustomer->setName($oCustomerRemote->getName());
            $newCustomer->setUser($newUser);
            $newUser->setCustomer($newCustomer);
            array_push($remoteCustomers,$oCustomerRemote->getId());


            $this->emLocal->persist($newCustomer);
            $this->emLocal->flush();
            array_push($newLocalCusomers,$newCustomer->getId());

            foreach ($oCustomerRemote->getPhonenumbers() as $phonenumber) {
                $newPhonenumber = new PhoneNumber();
                $newPhonenumber->setPhonenumber($phonenumber->getPhonenumber());
                $newPhonenumber->setDescription($phonenumber->getDescription());
                $newPhonenumber->setCustomer($newCustomer);
                $newCustomer->addPhonenumber($newPhonenumber);

                $this->emLocal->persist($newPhonenumber);
                array_push($remotePhonenumbers,$phonenumber->getId());
            }

            foreach ($oCustomerRemote->getAddresses() as $address) {
                $newAddress = new Address();
                $newAddress->setCity($address->getCity());
                $newAddress->setStreet($address->getStreet());
                $newAddress->setCompanyName($address->getCompanyName());
                $newAddress->setComplement($address->getComplement());
                $newAddress->setName($address->getName());
                $newAddress->setCountry($address->getCountry());
                $newAddress->setCustomer($newCustomer);
                $newCustomer->addAddress($newAddress);

                $this->emLocal->persist($newAddress);
                array_push($remoteAddresses,$address->getId());
            }



            foreach ($oCustomerRemote->getOrders() as $order) {
                $newOrder = new Order();
                $newOrder->setCustomer($newCustomer);
                $newOrder->setTot($order->getTot());
                $newOrder->setPayed($order->getPayed());
                $newOrder->setCreatedAt($order->getCreatedAt());
                $newOrder->setReference($order->getReference());

                $newOrder->setBillingAddressString($order->getBillingAddressString());
                $newOrder->setShippingAddressString($order->getShippingAddressString());

                foreach($order->getOrdersRealShippingMethods() as $orderRealShippingMethod){
                    $newOrderShippingMethod = new OrdersRealShippingMethods();
                    $newOrderShippingMethod->setOrder($newOrder);
                    $newOrderShippingMethod->setPrice($orderRealShippingMethod->getPrice());
                    $newOrderShippingMethod->setReference($orderRealShippingMethod->getReference());

                    $this->emLocal->persist($newOrderShippingMethod);
                    $this->emLocal->flush();

                    $remoteOrdersShippingMethods[$orderRealShippingMethod] = $newOrderShippingMethod->getId();

                    $newOrder->addOrdersRealShippingMethod($newOrderShippingMethod);
                }
                foreach($order->getOrdersOrderSteps() as $orderOrderStep){
                    $newOrderOrderStep = new OrdersOrderSteps();
                    $newOrderOrderStep->setCreatedAt($orderOrderStep->getCreatedAt());
                    $newOrderOrderStep->setOrder($newOrder);
                    $newOrderOrderStep->setDescription($orderOrderStep->getDescription());
                    $newOrderOrderStep->setOrderStep($orderOrderStep->getOrderStep());

                    $newOrder->addOrdersOrderStep($newOrderOrderStep);
                }
                foreach($order->getOrdersProducts() as $orderProduct){
                    $newOrderProduct = new OrdersProducts();
                    $newOrderProduct->setOrder($newOrder);
                    $newOrderProduct->setPrice($orderProduct->getPrice());
                    $newOrderProduct->setName($orderProduct->getNAme());
                    $newOrderProduct->setIsSecondHand($orderProduct->getIsSecondHand());
                    $newOrderProduct->setDescription($orderProduct->getDescription());
                    $newOrderProduct->setSerialNumber($orderProduct->getSerialNumber());
                    $newOrderProduct->setWarrantieString($orderProduct->getWarrantieString());


                    $newOrderShippingMethod = $this->emLocal->getRepository("LilWorksStoreBundle:OrdersRealShippingMethods")->find($remoteOrdersShippingMethods[$orderProduct->getOrderRealShippingMethod()]);
                    if($newOrderShippingMethod)
                        $newOrderProduct->setOrderRealShippingMethod($newOrderShippingMethod);

                    $newOrder->addOrdersProduct($newOrderProduct);
                }

                $this->emLocal->persist($newOrder);
                $this->emLocal->persist($newCustomer);
                array_push($remoteOrders,$order->getId());
            }

        }



        $this->emLocal->flush();


        var_dump(
            $remoteUsers,
            $remoteCustomers,
            $remotePhonenumbers,
            $remoteOrders
        );



        foreach($remoteCustomers as $customer){
           $customerToRemove = $this->emRemote->getRepository("LilWorksStoreBundle:Customer")->find($customer);
            $this->emRemote->remove($customerToRemove);
        }
        $this->emRemote->flush();
        foreach($remoteUsers as $remoteUser){
            $statement = $connectionRemote->prepare("DELETE FROM fos_user WHERE id = :id ");
            $statement->bindValue('id', $remoteUser);
            $statement->execute();
        }




        $pdoLocal = new PDO('mysql:dbname=storeLocal;host=127.0.0.1;port=8889;','root','root');
        $pdoRemote = new PDO('mysql:dbname=storeRemote;host=127.0.0.1;port=8889;','root','root');

        $grammar = new MySqlGrammar();
        $localConnection = new Connection($pdoLocal,$grammar);
        $remoteConnection = new Connection($pdoRemote,$grammar);
        $sync = new DbSync(new Transfer(new ShaHash(), $blockSize, $transferSize));
        $targetDb = "storeRemote";
        $sourceDb = "storeLocal";
        foreach($config['parameters']['objects'] as $object) {

            if (!isset($object["table"])) {
                $entityName = $object["name"];
                $table = $this->emLocal->getClassMetadata($entityName)->getTableName();

            } else {
                $table = $object["table"];
            }
            $sourceTable = new Table($remoteConnection, $sourceDb, $table);
            $targetTable = new Table($localConnection, $targetDb, $table);
            $syncResults = $sync->sync($sourceTable, $targetTable);

            echo "<h1>$table</h1>";

            var_dump($syncResults);

        }


        // SUPPRESSION DE TOUT DANS LA TABLE DISTANTE LOCALE
        $distantUsers=array();
        foreach($remoteCustomers as $remoteCustomerId){
            $distantCustomer = $this->emDistant->getRepository("LilWorksStoreBundle:Customer")->find($remoteCustomerId);
            array_push($distantUsers,$distantCustomer->getUser()->getId());
            $distantCustomer->setUser(null);
            $this->emDistant->persist($distantCustomer);
        }
        $this->emDistant->flush();

        $connectionDistant = $this->emDistant->getConnection();
        foreach($distantUsers as $userId){
            $statement = $connectionDistant->prepare("DELETE FROM fos_user WHERE id = :id ");
            $statement->bindValue('id', $userId);
            $statement->execute();
        }
*/

        /*
        $targetDb = "storeLocal";
        $sourceDb = "storeRemote";

        $pdoLocal = new PDO('mysql:dbname='.$config['parameters']['target']['dbname'].';host='.$config['parameters']['target']['host'].';port='.$config['parameters']['target']['port'].';','root','root');
        $pdoRemote = new PDO('mysql:dbname='.$config['parameters']['source']['dbname'].';host='.$config['parameters']['source']['host'].';port='.$config['parameters']['source']['port'].';','root',':Inpdqv:Ifv:');

        $grammar = new MySqlGrammar();
        $localConnection = new Connection($pdoLocal,$grammar);
        $remoteConnection = new Connection($pdoRemote,$grammar);

        $sync = new DbSync(new Transfer(new ShaHash(), $blockSize, $transferSize));

        $sync->dryRun(false);
        $sync->delete(true);

        foreach($config['parameters']['objects'] as $object) {

            if (!isset($object["table"])) {
                $entityName = $object["name"];
                $table = $this->emLocal->getClassMetadata($entityName)->getTableName();

            } else {
                $table = $object["table"];
            }
            $sourceTable = new Table($localConnection, $sourceDb, $table);
            $targetTable = new Table($remoteConnection, $targetDb, $table);
            $syncResults = $sync->sync($sourceTable, $targetTable);

            echo "<h1>$table</h1>";

            var_dump($syncResults);

        }
        */


    }
}