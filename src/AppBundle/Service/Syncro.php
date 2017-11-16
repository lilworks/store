<?php
namespace AppBundle\Service;


use Symfony\Component\Finder\Finder;

use DbSync\DbSync;
use DbSync\Transfer\Transfer;
use DbSync\Hash\ShaHash;
use DbSync\Table;
use DbSync\ColumnConfiguration;


use Database\Query\Grammars\MySqlGrammar;
use PDO;
use Database\Connection;

use Ijanki\Bundle\FtpBundle\Exception\FtpException;

class Syncro
{

    protected $container;

    protected $emLocal;
    protected $emDistant;

    protected $defaultConnection;
    protected $distantConnection;

    protected $paramsLocal;
    protected $paramsDistant;

    protected $dbs;

    protected $sync;
    protected $ftp;
    protected $cleaner;
    protected $session;

    public function __construct($container, \Doctrine\ORM\EntityManager $emDefault,\Doctrine\ORM\EntityManager $emDistant){


        $this->dbs = array(
            array('name'=>'lilworks_category', 'folder'=>'category' , 'icon'=>'fa fa-bullseye','dependencies'=>array('lilworks_supercategories_categories')),
            array('name'=>'lilworks_supercategory', 'folder'=>'supercategory','icon'=>'fa fa-object-group'),
            array('name'=>'lilworks_supercategories_categories','icon'=>array('fa fa-object-group','fa fa-bullseye'),'dependencies'=>array('lilworks_category','lilworks_supercategory')),
            array('name'=>'lilworks_brand', 'folder'=>'brand','icon'=>'fa fa-copyright','dependencies'=>array('lilworks_product')),
            array('name'=>'lilworks_tax','icon'=>'fa fa-pie-chart','dependencies'=>array('lilworks_products_taxesOnline')),
            array('name'=>'lilworks_products_taxesOnline','icon'=>array('fa fa-cube','fa fa-pie-chart'),'dependencies'=>array('lilworks_tax','lilworks_product')),
            array('name'=>'lilworks_warranty','icon'=>'fa fa-wrench','dependencies'=>array('lilworks_products_warranties_online')),
            array('name'=>'lilworks_products_warranties_online','icon'=>array('fa fa-cube','fa fa-wrench'),'dependencies'=>array('lilworks_warranty','lilworks_product')),
            array('name'=>'lilworks_picture', 'folder'=>'product','icon'=>'fa fa-cube','dependencies'=>array('lilworks_product')),
            array('name'=>'lilworks_docfile', 'folder'=>'docfile/product','dependencies'=>array('lilworks_products_docfiles')),
            array('name'=>'lilworks_products_docfiles','icon'=>array('fa fa-cube','fa fa-file-text'),'dependencies'=>array('lilworks_docfile','lilworks_product')),
            array('name'=>'lilworks_tag','icon'=>'fa fa-tag','dependencies'=>array('lilworks_products_tags')),
            array('name'=>'lilworks_products_tags','icon'=>array('fa fa-tag','fa fa-bullseye'),'dependencies'=>array('lilworks_tag','lilworks_product')),
            array('name'=>'lilworks_product','icon'=>'fa fa-cube','dependencies'=>array('lilworks_brand')),
            array('name'=>'lilworks_products_categories','icon'=>array('fa fa-cube','fa fa-bullseye'),'dependencies'=>array('lilworks_category','lilworks_product')),
            array('name'=>'lilworks_annonce','icon'=>'fa fa-bullhorn'),
            array('name'=>'lilworks_text', 'folder'=>'ajaxupload' ,'icon'=>'fa fa-file-text')
        );

        $this->cleaner = $container->get("lilworks.store.entity.file.cleaner");
        $this->session = $container->get("session");

        $this->container = $container;
        try {
            $this->ftp = $this->container->get('ijanki_ftp');

            $this->ftp->connect($this->container->getParameter('ftp_host_syncro'),$this->container->getParameter('ftp_port_syncro') );
            $this->ftp->login($this->container->getParameter('ftp_user_syncro'), $this->container->getParameter('ftp_password_syncro'));
            $this->ftp->pasv(true);

        } catch (FtpException $e) {
            echo 'Error: ', $e->getMessage();
        }


        $this->emDefault = $emDefault;
        $this->emDistant = $emDistant;

        $this->paramsDefault = $emDefault->getConnection()->getParams();
        $this->paramsDistant = $emDistant->getConnection()->getParams();


        $pdoDefault = new PDO('mysql:dbname='.$this->paramsDefault["dbname"].';host='.$this->paramsDefault["host"].';port='.$this->paramsDefault["port"].';',$this->paramsDefault["user"],$this->paramsDefault["password"]);
        $pdoDistant = new PDO('mysql:dbname='.$this->paramsDistant["dbname"].';host='.$this->paramsDistant["host"].';port='.$this->paramsDistant["port"].';',$this->paramsDistant["user"],$this->paramsDistant["password"]);

        $grammar = new MySqlGrammar();

        $this->defaultConnection = new Connection($pdoDefault,$grammar);
        $this->distantConnection = new Connection($pdoDistant,$grammar);

        $this->sync = new DbSync(new Transfer(new ShaHash(), 1024, 8));

        return $this;

    }

    public function statusDb(){
        $this->sync->dryRun(true);
        foreach($this->dbs as $k=>$db){
            $r=$this->dbsync($db['name'] , true);
            if($r->getRowsTransferred()>0){
                $this->dbs[$k]['syncro']=false;
            }else{
                $this->dbs[$k]['syncro']='true';
            }
        }

        /*
        foreach($this->dbs as $k=>$db){
            if(isset($db['dependencies'])){
                foreach($db['dependencies'] as $k2=>$v2){
                    foreach($this->dbs as $k3=>$v3){
                        if($v3['name'] == $v2){
                            if(!isset($this->dbs[$k]['dStatus']))
                                $this->dbs[$k]['dStatus'] = array();
                            $this->dbs[$k]['dStatus'][$k2]=$this->dbs[$k]['syncro'];
                            break 1;
                        }
                    }
                }
            }
        }
        */


        return $this->dbs;
    }




    public function findInFolder($dir){
        $dir = $this->container->getParameter('kernel.root_dir') . "/../web/" . $dir;
        $finder = new Finder();
        $finder->files()->in($dir);

        $files = array();

        foreach ($finder as $file) {
            array_push($files,$file->getRelativePathname());
            /*
            array_push($files,array(
                'realPath'=>$file->getRealPath(),
                'relativePath'=>$file->getRelativePath(),
                'relativePathname'=>$file->getRelativePathname(),
            ));
            */
        }

        return $files;
    }





    public function dbsync($tablename ,$dry){
        if($dry === true){
            $this->sync->dryRun(true);
        }else{
            $this->sync->dryRun(false);
            $this->sync->delete(true);
        }
        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], $tablename);
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], $tablename);
        return $this->sync->sync($sourceTable, $targetTable);
    }

    public function syncroFolder($folder){
        $files = $this->findInFolder($folder);
        $this->ftp->chdir($folder);

        $result = [
            "deletedUnusedFiles"=>[],
            "alreadyInFiles"=>[],
            "uploadedFiles"=>[],
        ];
        foreach($this->ftp->nlist("./") as $distantFile){
            if(!in_array($distantFile,$files)){
                $this->ftp->delete($distantFile);
                array_push($result['deletedUnusedFiles'],$distantFile);
            }
        }

        foreach($files as $file){
            if($this->ftp->size($file) != filesize($this->container->getParameter('kernel.root_dir') . "/../web/".$folder."/" . $file)){
                $this->ftp->put($file, $this->container->getParameter('kernel.root_dir') . "/../web/".$folder."/" . $file, FTP_BINARY);
                $this->ftp->chmod(0777, $file);
                array_push($result['uploadedFiles'],$file);
            }else{
                array_push($result['alreadyInFiles'],$file);
            }
        }
        return $result;
    }



}