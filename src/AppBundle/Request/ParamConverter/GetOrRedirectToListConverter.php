<?php
namespace AppBundle\Request\ParamConverter;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;


/**
 * Class EntityConverter.
 */
class GetOrRedirectToListConverter implements ParamConverterInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    private $router;

    /**
     * @var ManagerRegistry Manager registry
     */
    private $registry;

    /**
     * @param ManagerRegistry        $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager,Router $router)
    {
        $this->registry = $registry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {

        if ('app_get_or_redirect_to_list_converter' !== $configuration->getConverter()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Applies converting
     *
     * @throws \InvalidArgumentException When route attributes are missing
     * @throws NotFoundHttpException     When object not found
     */
    public function apply(Request $request, ParamConverter $configuration)
    {

        $name = $configuration->getName();
        $options = $configuration->getOptions();
        $class = $configuration->getClass();
        $repository = $this->entityManager->getRepository($class);


        $a = array_keys($configuration->getOptions()['mapping']);
        $id = $request->attributes->get($a[0]);


        $repositoryMethod = $options['repository_method'];
        $entity = $repository->$repositoryMethod($id);


        if(!$entity){
            $request->attributes->set($name, null);
        }else{
            $request->attributes->set($name, $entity);
        }
    }
}