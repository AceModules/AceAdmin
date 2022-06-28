<?php

namespace AceAdmin\Factory;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Annotation\EntityBasedFormBuilder as AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Laminas\Form\Factory;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AdminControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get(EntityManager::class);
        $config        = $container->get('Config');

        $annotationBuilder = new AnnotationBuilder($entityManager);

        return new $requestedName($entityManager, $annotationBuilder, $config['ace_admin'] ?? []);
    }
}
