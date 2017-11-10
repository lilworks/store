<?php
namespace AppBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use LilWorks\StoreBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends Controller
{
    /**
     * Show the user.
     */
    public function showAction()
    {
        $basket = $this->get('site.basket');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $translator = $this->get('translator');
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($translator->trans('sitebundle.profile.show') . " - " . $seoPage->getTitle() )
            ->addMeta('property', 'og:title', $translator->trans('sitebundle.profile.show') . " - " . $seoPage->getTitle())
        ;
        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
            'basket'=>$basket
        ));
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {

        $this->get('site.setSeo')->setTitle('profile.edit.submit',array(),'FOSUserBundle');


        $myRequest = $request->request;


        $basket = $this->get('site.basket');
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $originalPhonenumbers = new ArrayCollection();
        foreach ($user->getCustomer()->getPhonenumbers() as $phonenumber) {
            $originalPhonenumbers->add($phonenumber);
        }

        $originalAddresses = new ArrayCollection();
        foreach ($user->getCustomer()->getAddresses() as $address) {
            $originalAddresses->add($address);
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }


        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();


        $form->setData($user);
        $form->handleRequest($request);




        if ($form->isSubmitted() && $form->isValid()) {


            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);


            if (null === $response = $event->getResponse()) {
                #$url = $this->generateUrl('fos_user_profile_show');
                $url = $this->generateUrl('fos_user_profile_edit');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            $translator = $this->get('translator');
            $seoPage = $this->container->get('sonata.seo.page');
            $seoPage
                ->setTitle($translator->trans('sitebundle.profile.edit') . " - " . $seoPage->getTitle() )
                ->addMeta('property', 'og:title', $translator->trans('sitebundle.profile.edit') . " - " . $seoPage->getTitle())
            ;


            #return $response;
        }

        $translator = $this->get('translator');
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle( $seoPage->getTitle(). " - " . $translator->trans('sitebundle.htmltitle.user.edit'));


        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'request'=>$myRequest,
            'form' => $form->createView(),
            'basket'=>$basket
        ));
    }
}
