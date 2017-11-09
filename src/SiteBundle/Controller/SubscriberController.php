<?php
namespace SiteBundle\Controller;

use LilWorks\StoreBundle\Entity\Subscriber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SubscriberController extends Controller
{


    public function unsubscribeAction(Request $request,$email = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if(!$email && is_object($user)){
            $byUser = $em->getRepository('LilWorksStoreBundle:Subscriber')->findOneByUser($user->getId());
            $byEmail = $em->getRepository('LilWorksStoreBundle:Subscriber')->findOneByEmail($user->getEmail());

            if($byUser){
                $user->setSubscriber(null);
                $em->remove($byUser);
            }elseif($byEmail){
                $em->remove($byEmail);
            }


        }elseif($email){
            $byEmail = $em->getRepository('LilWorksStoreBundle:Subscriber')->findOneByEmail($user->getEmail());
            $em->remove($byEmail);
        }

        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('site_customer');
        } else {
            return $this->redirect($referer);
        }

    }

    public function subscribeAction(Request $request,$email = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if(!$email && is_object($user)){
            $byUser = $em->getRepository('LilWorksStoreBundle:Subscriber')->findOneByUser($user->getId());
            $byEmail = $em->getRepository('LilWorksStoreBundle:Subscriber')->findOneByEmail($user->getEmail());

            $subscriber = new Subscriber();

            if(!$byUser && !$byEmail){
                $subscriber->setUser($user);
                $user->setSubscriber($subscriber);
                $em->persist($subscriber);
            }elseif($byEmail){
                $subscriber = $byEmail;
                $subscriber->setEmail(null);
                $subscriber->setUser($user);
                $user->setSubscriber($subscriber);
                $em->persist($subscriber);
            }

        }elseif($email){
            if(!$byEmail = $em->getRepository('LilWorksStoreBundle:Subscriber')->findOneByEmail($user->getEmail())){
                $subscriber = new Subscriber();
                $subscriber->setEmail($email);
                $em->persist($subscriber);
            }

        }

        $em->flush();

        $referer = $request->headers->get('referer');
        if ( !$referer || is_null($referer) ) {
            return $this->redirectToRoute('site_customer');
        } else {
            return $this->redirect($referer);
        }

    }
}