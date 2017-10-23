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

    protected $sync;
    protected $ftp;
    protected $cleaner;
    protected $session;

    public function __construct($container, \Doctrine\ORM\EntityManager $emDefault,\Doctrine\ORM\EntityManager $emDistant){

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

    public function annonces($dry = true){
        $ftpResult  = array();
        if($dry === true){
            $this->sync->dryRun(true);
        }else{
            // clean local folder of unused image
            $this->cleaner->clean("annonce","LilWorksStoreBundle:Annonce","pictureName");
            $this->sync->dryRun(false);
            $this->sync->delete(true);
            $ftpResult = $this->syncroFolder('annonce');
        }

        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], 'lilworks_annonce');
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], 'lilworks_annonce');
        $dbResult = $this->sync->sync($sourceTable, $targetTable);



        if($dry === false){
            $this->session->getFlashBag()->clear();
            $this->session->getFlashBag()->add(
                'syncro',
                array( 'name'=>'annonces', 'ftpResult'=>$ftpResult,'dbResult'=>$dbResult)
            );
        }
        return $this->sync->sync($sourceTable, $targetTable);
    }
    public function products($dry = true){
        $ftpResult  = array();
        if($dry === true){
            $this->sync->dryRun(true);
        }else{
            // clean local folder of unused image
            $this->cleaner->clean("product","LilWorksStoreBundle:Picture","pictureName");
            $this->sync->dryRun(false);
            $this->sync->delete(true);
            $ftpResult = $this->syncroFolder('product');
        }

        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], 'lilworks_product');
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], 'lilworks_product');

        $dbResult = $this->sync->sync($sourceTable, $targetTable);

        if($dry === false) {
            $this->session->getFlashBag()->clear();
            $this->session->getFlashBag()->add(
                'syncro',
                array('name'=>'products','ftpResult' => $ftpResult, 'dbResult' => $dbResult)
            );
        }

        return $this->sync->sync($sourceTable, $targetTable);
    }

    public function texts($dry = true){
        if($dry === true){
            $this->sync->dryRun(true);
            $this->sync->delete(true);
        }else{
            $this->sync->dryRun(false);
        }

        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], 'lilworks_text');
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], 'lilworks_text');

        // if you only want specific columns
        #$columnConfig = new ColumnConfiguration(array('stock'), array());

        return $this->sync->sync($sourceTable, $targetTable);
    }

    public function brands($dry = true){
        $ftpResult  = array();
        if($dry === true){
            $this->sync->dryRun(true);
        }else{
            // clean local folder of unused image
            $this->cleaner->clean("brand","LilWorksStoreBundle:Brand","pictureName");
            $this->sync->dryRun(false);
            $this->sync->delete(true);
            $ftpResult = $this->syncroFolder('brand');
        }

        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], 'lilworks_brand');
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], 'lilworks_brand');

        $dbResult = $this->sync->sync($sourceTable, $targetTable);

        if($dry === false) {
            $this->session->getFlashBag()->clear();
            $this->session->getFlashBag()->add(
                'syncro',
                array('name'=>'brands','ftpResult' => $ftpResult, 'dbResult' => $dbResult)
            );
        }

        return $this->sync->sync($sourceTable, $targetTable);
    }
    public function categories($dry = true){
        $ftpResult  = array();
        if($dry === true){
            $this->sync->dryRun(true);
        }else{
            // clean local folder of unused image
            $this->cleaner->clean("category","LilWorksStoreBundle:Category","pictureName");
            $this->sync->dryRun(false);
            $this->sync->delete(true);
            $ftpResult = $this->syncroFolder('category');
        }

        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], 'lilworks_category');
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], 'lilworks_category');

        $dbResult = $this->sync->sync($sourceTable, $targetTable);

        if($dry === false) {
            $this->session->getFlashBag()->clear();
            $this->session->getFlashBag()->add(
                'syncro',
                array('name'=>'categories','ftpResult' => $ftpResult, 'dbResult' => $dbResult)
            );
        }

        // update manyToMany assoc
        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], 'lilworks_supercategories_categories');
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], 'lilworks_supercategories_categories');
        $this->sync->sync($sourceTable, $targetTable);


        return $dbResult;
    }
    public function supercategories($dry = true){
        $ftpResult  = array();
        if($dry === true){
            $this->sync->dryRun(true);
        }else{
            // clean local folder of unused image
            $this->cleaner->clean("supercategory","LilWorksStoreBundle:SuperCategory","pictureName");
            $this->sync->dryRun(false);
            $this->sync->delete(true);
            $ftpResult = $this->syncroFolder('supercategory');
        }

        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], 'lilworks_supercategory');
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], 'lilworks_supercategory');

        $dbResult = $this->sync->sync($sourceTable, $targetTable);

        if($dry === false) {
            $this->session->getFlashBag()->clear();
            $this->session->getFlashBag()->add(
                'syncro',
                array('name'=>'supercategories','ftpResult' => $ftpResult, 'dbResult' => $dbResult)
            );
        }

        // update manyToMany assoc
        $sourceTable = new Table($this->defaultConnection, $this->paramsDefault["dbname"], 'lilworks_supercategories_categories');
        $targetTable = new Table($this->distantConnection, $this->paramsDistant["dbname"], 'lilworks_supercategories_categories');
        $this->sync->sync($sourceTable, $targetTable);

        return $dbResult;
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

    private function syncroFolder($folder){
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