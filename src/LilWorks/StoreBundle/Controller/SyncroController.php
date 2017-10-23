<?php
namespace LilWorks\StoreBundle\Controller;


use Database\Query\Grammars\MySqlGrammar;
use PDO;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\FileLocator;


use DbSync\DbSync;
use DbSync\Transfer\Transfer;
use DbSync\Hash\ShaHash;
use DbSync\Table;
use DbSync\ColumnConfiguration;
use Database\Connection;

class SyncroController extends Controller
{


    /**
     * Clean the picture folder
     *
     */
    public function cleanEntityFolderAction(Request $request)
    {
        $todo = array(
            "lilworks.storebundle.product"=>array("product","LilWorksStoreBundle:Picture","pictureName"),
            "lilworks.storebundle.brand"=>array("brand","LilWorksStoreBundle:Brand","pictureName"),
            "lilworks.storebundle.category"=>array("category","LilWorksStoreBundle:Category","pictureName"),
            "lilworks.storebundle.supercategory"=>array("supercategory","LilWorksStoreBundle:SuperCategory","pictureName"),
            "lilworks.storebundle.annonce"=>array("annonce","LilWorksStoreBundle:Annonce","pictureName"),
            "lilworks.storebundle.shippingmethod"=>array("shippingmethod","LilWorksStoreBundle:ShippingMethod","pictureName"),
            "lilworks.storebundle.paymentmethod"=>array("paymentmethod","LilWorksStoreBundle:PaymentMethod","pictureName"),
        );
        $done = array();
        foreach($todo as $k => $cleanparam){
            $done[$k] = $this->get("lilworks.store.entity.file.cleaner")->clean($cleanparam[0],$cleanparam[1],$cleanparam[2]);
        }
        return $this->render('LilWorksStoreBundle:Syncro:clean.html.twig', array(
            'done'=>$done
        ));
    }




    public function stockAction()
    {



        $emLocal = $this->getDoctrine()->getEntityManager('default');
        $emRemote = $this->getDoctrine()->getEntityManager('distant');


        // if offline first need to retreive stock from online
        $onlineDestockings = $emRemote->getRepository("LilWorksStoreBundle:OnlineDestocking")->findAll();
        foreach( $onlineDestockings as $onlineDestocking  ){


            $q  = $onlineDestocking->getOrderProduct()->getQuantity();
            $p = $onlineDestocking->getOrderProduct()->getProduct();

            $product = $emLocal->getRepository("LilWorksStoreBundle:Product")->find($p->getId());
            $product->setStock($product->getStock() - ($q) );

            $emRemote->remove($onlineDestocking);
            $emLocal->persist($product);

        }
        $emRemote->flush();
        $emLocal->flush();



        $paramsLocal = $emLocal->getConnection()->getParams();
        $paramsRemote = $emRemote->getConnection()->getParams();


        $pdoLocal = new PDO('mysql:dbname='.$paramsLocal["dbname"].';host='.$paramsLocal["host"].';port='.$paramsLocal["port"].';',$paramsLocal["user"],$paramsLocal["password"]);
        $pdoRemote = new PDO('mysql:dbname='.$paramsRemote["dbname"].';host='.$paramsRemote["host"].';port='.$paramsRemote["port"].';',$paramsRemote["user"],$paramsRemote["password"]);

        $grammar = new MySqlGrammar();
        $localConnection = new Connection($pdoLocal,$grammar);
        $remoteConnection = new Connection($pdoRemote,$grammar);



        $sync = new DbSync(new Transfer(new ShaHash(), 1024, 8));

        $sync->dryRun(true);


        $sourceTable = new Table($localConnection, $paramsLocal["dbname"], 'lilworks_product');
        $targetTable = new Table($remoteConnection, $paramsRemote["dbname"], 'lilworks_product');

        // if you only want specific columns
        $columnConfig = new ColumnConfiguration(array('stock'), array());

        $result = $sync->sync($sourceTable, $targetTable, $columnConfig);




        // after that update online

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.syncro.stock'));

        return $this->render('LilWorksStoreBundle:Syncro:index.html.twig', array(
            'result'=>$result

        ));


    }

    public function productAction()
    {
        $syncro = $this->get('app.syncro');
        $products = $syncro->products(false);

        return $this->redirectToRoute('syncro_index');
    }


    public function annonceAction()
    {
        $syncro = $this->get('app.syncro');
        $annonces = $syncro->annonces(false);
        return $this->redirectToRoute('syncro_index');
    }

    public function textAction()
    {
        $syncro = $this->get('app.syncro');
        $annonces = $syncro->texts(false);
        return $this->redirectToRoute('syncro_index');
    }
    public function brandAction()
    {
        $syncro = $this->get('app.syncro');
        $annonces = $syncro->brands(false);
        return $this->redirectToRoute('syncro_index');
    }


    public function categoryAction()
    {
        $syncro = $this->get('app.syncro');
        $annonces = $syncro->categories(false);
        return $this->redirectToRoute('syncro_index');
    }


    public function supercategoryAction()
    {
        $syncro = $this->get('app.syncro');
        $annonces = $syncro->supercategories(false);
        return $this->redirectToRoute('syncro_index');
    }



    public function indexAction()
    {

        $syncro = $this->get('app.syncro');

        $products = $syncro->products(true);
        $brands = $syncro->brands(true);
        $categories = $syncro->categories(true);
        $supercategories = $syncro->supercategories(true);
        $texts = $syncro->texts(true);
        $annonces = $syncro->annonces(true);



        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.syncro.index'));

        /*
         if( FALSE === $this->getDoctrine()->getEntityManager('distant')->getConnection()->isConnected() ){
            return $this->render('LilWorksStoreBundle:Syncro:no-connection.html.twig',array());
        }
*/

        return $this->render('LilWorksStoreBundle:Syncro:index.html.twig',array(
            'onlineDestockings'=>$this->getDoctrine()->getEntityManager('distant')->getRepository("LilWorksStoreBundle:OnlineDestocking")->findAll(),
            'products'=>$products,
            'texts'=>$texts,
            'brands'=>$brands,
            'categories'=>$categories,
            'supercategories'=>$supercategories,
            'annonces'=>$annonces,
            'flashBag'=>$this->get('session')->getFlashBag()->get('syncro')
        ));



       #$this->get('app.syncro')->getNewRemoteOrders();
       # $this->get('app.syncro')->test();



        /*
        $emRemote = $this->getDoctrine()->getEntityManager('online');
        $myFuckingUserRemote = $emRemote->getRepository("LilWorksStoreBundle:Order")->find(7782);
        $a = $myFuckingUserRemote->getCustomer()->getAddresses();
        var_dump(
            $a[0]->getCity()

            );
        die();
        $myFuckingCustomerRemote = $myFuckingUserRemote->getCustomer();
        $myFuckingOrderRemote = $myFuckingCustomerRemote->getOrders();


        $emLocal = $this->getDoctrine()->getEntityManager();

        $mynewUser =  new User();
        $mynewUser = clone $myFuckingUserRemote;
        $mynewUser->setId(null);
        $emLocal->persist($mynewUser);

        $metadata = $emLocal->getClassMetaData(get_class($mynewUser));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        //$emLocal->flush();
        die();

        $configDirectories = array(__DIR__.'/../Resources/config/parameters');
        $locator = new FileLocator($configDirectories);
        $loaderResolver = new LoaderResolver(array(new YamlSyncroLoader($locator)));
        $delegatingLoader = new DelegatingLoader($loaderResolver);


        $config = $delegatingLoader->load(__DIR__.'/../Resources/config/parameters/syncro.yml');



        $finder = new Finder();





        try {
            $ftp = $this->container->get('ijanki_ftp');
            $ftp->connect($config['parameters']["remote"]["ftp"]["host"]);
            $ftp->login(
                $config['parameters']["remote"]["ftp"]["user"],
                $config['parameters']["remote"]["ftp"]["password"]
            );
        } catch (FtpException $e) {
            echo 'Error: ', $e->getMessage();
        }



        $em = $this->getDoctrine()->getManager();

        $blockSize=1024;
        $transferSize=8;
        $sync = new DbSync(new Transfer(new ShaHash(), $blockSize, $transferSize));

        $targetDb = "storeOnline";
        $sourceDb = "storeOffline";

        $pdoOffline = new PDO('mysql:dbname=storeOffline;host=127.0.0.1;port=8889;','root','root');
        $pdoOnline = new PDO('mysql:dbname=storeOnline;host=127.0.0.1;port=8889;','root','root');
        $grammar = new MySqlGrammar();

        foreach($config['parameters']['objects'] as $object){

            if(!isset($object["table"])){
               $entityName = $object["name"];
               $table = $em->getClassMetadata($entityName)->getTableName();

           }else{
               $table =  $object["table"];
           }



            $sourceTable = $targetTable = $table;


            $sync->dryRun(false);

            $sync->delete(false);

            $onlineConnection = new Connection($pdoOnline,$grammar);
            $offlineConnection = new Connection($pdoOffline,$grammar);


            $sourceTable = new Table($offlineConnection, $sourceDb, $sourceTable);
            $targetTable = new Table($onlineConnection, $targetDb, $targetTable);
            $syncResult = $sync->sync($sourceTable, $targetTable);

            echo "<h1>$table</h1>";
            foreach($config['parameters']['objects'] as $object) {
                if(isset($object["folders"]) && count($object["folders"])>0) {

                    foreach ($object["folders"] as $folder) {

                        $remoteList = $ftp->nlist($config['parameters']["remote"]["ftp"]["directory"]."/".$folder);

                        $finder->files()->in($folder);
                        foreach ($finder as $file) {
                            // Dump the absolute path
                      //      var_dump($file->getRealPath());

                            // Dump the relative path to the file, omitting the filename
                        //    var_dump($file->getRelativePath());

                            // Dump the relative path to the file
                          //  var_dump($file->getRelativePathname());
                            if(!in_array($file->getRelativePathname(),$remoteList)){

                                echo $file->getRealPath();
                                echo    "<br>";

                                echo    "<br></hr>";

                                $ftp->put(
                                    $config['parameters']["remote"]["ftp"]["directory"]."/".$folder."/".$file->getRelativePathname(),
                                    $file->getRealPath(),
                                     FTP_BINARY
                                    );
                            }

                        }
                    }
                }
            }


        }

die();
*/

    }

}
