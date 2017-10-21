<?php
namespace LilWorks\StoreBundle\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Voter based on the uri
 */
class RequestVoter implements VoterInterface
{

    private $requestStack;
    private $logger;

    public function __construct(RequestStack $requestStack ,LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * Checks whether an item is current.
     *
     * If the voter is not able to determine a result,
     * it should return null to let other voters do the job.
     *
     * @param ItemInterface $item
     * @return boolean|null
     */
    public function matchItem(ItemInterface $item)
    {
/*
        if ($item->getUri() === $this->requestStack->getCurrentRequest()->getRequestUri()) {
            $item->getParent()->removeChild($item->getName());

            #return true;
        }
*/

        return null;
    }
}