<?php
namespace SiteBundle\Service\Payment;



interface PaymentInterface
{



    public function getForm();
    public function getRedirection();
    public function getText();
    public function getName();


}
