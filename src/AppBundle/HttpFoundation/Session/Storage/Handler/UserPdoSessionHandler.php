<?php
namespace AppBundle\HttpFoundation\Session\Storage\Handler;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\Security\Core\User\UserInterface;

class UserPdoSessionHandler extends PdoSessionHandler
{

    protected  $em;
    protected  $pdo;
    protected  $dbOptions;
    protected  $context;
    protected  $driver;
    protected  $table;
    protected  $dataCol;
    protected  $lifetimeCol;
    protected  $idCol;
    protected  $timeCol;
    protected $inTransaction;
    protected $userCol;

    public function __construct( $pdo, array $dbOptions = array(), $context,\Doctrine\ORM\EntityManager $em)
    {

        $this->em = $em;
        parent::__construct($pdo, $dbOptions);

        $this->context = $context;
        $this->dbOptions = $dbOptions;
        $this->pdo = $this->getConnection();
        $this->driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $this->table = "sessions";
        $this->dataCol = "sess_data";
        $this->lifetimeCol = "sess_lifetime";
        $this->idCol = "sess_id";
        $this->timeCol = "sess_time";
        $this->userCol = "user";

    }

    /**
     * {@inheritDoc}
     */
    public function write($id, $data)
    {
        $session = $this->em->getRepository('AppBundle:Session')->find($id);
        if( $this->context->getToken() && $this->context->getToken()->getUser() && !is_string($this->context->getToken()->getUser()) ){
            $user = $this->em->getRepository('AppBundle:User')->find($this->context->getToken()->getUser()->getId());
            if(!$session->getUser()){
                $session->setUser($user);
                $user->addSession($session);
                $this->em->persist($session);
                $this->em->flush();
            }
        }elseif($session && $session->getUser()){
            $session->getUser()->removeSession($session);
            $session->setUser(null);
            $this->em->persist($session);
            $this->em->flush();
        }
        parent::write($id,$data);
    }
}