<?php

namespace AceAdmin\Factory;

use AceAdmin\Form\Element\ObjectLiveSearch;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ObjectLiveSearchFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return ObjectLiveSearch
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get(EntityManager::class);
        $router        = $container->get('Router');
        $element       = new ObjectLiveSearch();

        $element->getProxy()->setObjectManager($entityManager);
        $element->setRouter($router);

        return $element;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container->getServiceLocator(), ObjectLiveSearch::class);
    }
}
