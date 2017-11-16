<?php
namespace SiteBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;
use LilWorks\StoreBundle\Form\AddressType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
class MessageInConversationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
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