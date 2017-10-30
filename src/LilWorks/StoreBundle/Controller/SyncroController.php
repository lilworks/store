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
            "storebundle.product"=>array("product","LilWorksStoreBundle:Picture","pictureName"),
            "storebundle.brand"=>array("brand","LilWorksStoreBundle:Brand","pictureName"),
            "storebundle.category"=>array("category","LilWorksStoreBundle:Category","pictureName"),
            "storebundle.supercategory"=>array("supercategory","LilWorksStoreBundle:SuperCategory","pictureName"),
            "storebundle.annonce"=>array("annonce","LilWorksStoreBundle:Annonce","pictureName"),
            "storebundle.shippingmethod"=>array("shippingmethod","LilWorksStoreBundle:ShippingMethod","pictureName"),
            "storebundle.paymentmethod"=>array("paymentmethod","LilWorksStoreBundle:PaymentMethod","pictureName"),
        );
        $done = array();
        foreach($todo as $k => $cleanparam){
            $done[$k] = $this->get("lilworks.store.entity.file.cleaner")->clean($cleanparam[0],$cleanparam[1],$cleanparam[2]);
        }
        return $this->render('LilWorksStoreBundle:Syncro:clean.html.twig', array(
            'done'=>$done
        ));
    }

    /**
     * Clean the picture folder
     *
     */
    public function cleanTagAction(Request $request)
    {
        $todo = array(
            "storebundle.product"=>array("LilWorksStoreBundle:Product","tag"),
            "storebundle.brand"=>array("LilWorksStoreBundle:Brand","tag"),
            "storebundle.category"=>array("LilWorksStoreBundle:Category","tag"),
            "storebundle.supercategory"=>array("LilWorksStoreBundle:SuperCategory","tag"),
            "storebundle.annonce"=>array("LilWorksStoreBundle:Annonce","tag"),
        );
        $done = array();
        foreach($todo as $k => $cleanparam){
            $done[$k] = $this->get("lilworks.store.entity.tag.cleaner")->clean($cleanparam[0],$cleanparam[1]);
        }
        return $this->render('LilWorksStoreBundle:Syncro:cleanTag.html.twig', array(
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


    public function actionAction($what)
    {
        $syncro = $this->get('app.syncro');
        $config = $syncro->statusDb();

        foreach($config as $v){
            if(isset($v['folder'])){
                $syncro->syncroFolder($v['folder']);
                break;
            }
        }

        $syncro->dbsync($what,false);

        return $this->redirectToRoute('syncro_index');
    }
    public function indexAction()
    {

        $syncro = $this->get('app.syncro');

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($translator->trans('storebundle.htmltitle.syncro.index'));

        /*
         if( FALSE === $this->getDoctrine()->getEntityManager('distant')->getConnection()->isConnected() ){
            return $this->render('LilWorksStoreBundle:Syncro:no-connection.html.twig',array());
        }
*/


        return $this->render('LilWorksStoreBundle:Syncro:index.html.twig',array(
            'flashBag'=>$this->get('session')->getFlashBag()->get('syncro'),
            'syncro'=> $syncro->statusDb()
        ));


    }

}
