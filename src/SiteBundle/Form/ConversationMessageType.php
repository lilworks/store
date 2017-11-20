<?php
namespace SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ConversationMessageType extends AbstractType
{
    private $user;
    private $em;

    public function __construct(TokenStorage $token_storage,\Doctrine\ORM\EntityManager $em)
    {

        $this->em = $em;
        $this->user = $token_storage->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if(is_object($this->user) && $this->user->getId()){
            $conversations = $this->em->getRepository('LilWorksStoreBundle:Conversation')->findByUser($this->user->getId());
            $dataEmail = $this->user->getEmail();
        }else{
            $dataEmail = null;
        }


        if(isset($conversations) && count($conversations)>0){

            $builder->add('conversation', EntityType::class, array(
                'label'=>'sitebundle.conversation',
                'class'    => 'LilWorksStoreBundle:Conversation' ,
                'required' => false ,
                'mapped'=> true,
                'expanded' => false ,
                'multiple' => false,
                'choice_label' => function ($obj) {
                    return  $obj->getConversationSubject();
                },
                'query_builder' => function (EntityRepository $er) use ($conversations)  {
                    return $er->createQueryBuilder('c')
                        ->where('c.id IN (:ids)')
                        ->setParameter('ids',$conversations)
                        ->orderBy('c.createdAt','desc')
                        ;
                },
            ))
            ;
        }
        $builder
            ->add('email',EmailType::class, array(
                'label'=>'sitebundle.conversationmessage.email',
                'mapped'=>false,
                'required'=>true,
                'data'=>$dataEmail
            ))
            ->add('messageSubject',null, array(
                'label'=>'sitebundle.conversationmessage.subject',
            ))
            ->add('messageBody',null, array(
                'label'=>'sitebundle.conversationmessage.body',
                'required'=>true,
                'attr'=>['class'=>'editor-text-message']
            ))
            ->add('getCopy',null, array(
                'label'=>'sitebundle.conversationmessage.getcopy',
            ))->add('save', SubmitType::class, array(
                'label'=>'sitebundle.button.send',
                'attr' => array('class' => 'save'),
            ));





    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LilWorks\StoreBundle\Entity\ConversationMessage',
            'user'=>null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sitebundle_conversation_message';
    }

}