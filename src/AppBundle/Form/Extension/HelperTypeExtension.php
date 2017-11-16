<?php
namespace AppBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolver;


class HelperTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {

        return FormType::class;
    }
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefined(array('help'));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {


        if (isset($options['help'])) {
            $view->vars['help'] = $options['help'];
        }
    }
}
