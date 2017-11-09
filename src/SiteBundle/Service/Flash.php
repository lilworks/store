<?php
namespace SiteBundle\Service;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class Flash
{

    private $session;
    private $translator;
    private $status = array(
       'success'=>'success',
        'error'=>'warning',
        'info'=>'info'
    );

    public function __construct(Session $session,TranslatorInterface $translator)
    {

        $this->session = $session;
        $this->translator = $translator;
    }


    public function setMessages($messages){

        $this->session->start();
        foreach($messages as $message){

            if(!isset($message['transParam']))
                $message['transParam'] = array();
            if(!isset($message['domain']))
                $message['domain'] = "messages";

            $this->session->getFlashBag()->add(
                $message['status'],
                $this->translator->trans($message['message'],$message['transParam'],$message['domain'])
            );
        }



    }
    public function getMessages($action = "action"){

        $out = "";
        foreach ($this->status as $kStatus=>$status) {
            foreach ($this->session->getFlashBag()->get($kStatus, array()) as $message) {
                $out.= '<div class="alert alert-'.$status.' alert-dismissible fade show" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      '.$message.'
                    </div>';
            }
        }


        return $out;
    }

}
