<?php

namespace AceAdmin\Factory;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Zend\Form\Factory;
use Zend\ServiceManager\Factory\FactoryInterface;

class AdminControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get(EntityManager::class);
        $config        = $container->get('Config');

        $annotationBuilder = new AnnotationBuilder($entityManager);
        $annotationBuilder->setFormFactory(new Factory($container->get('FormElementManager')));

        return new $requestedName($entityManager, $annotationBuilder, $config['ace_admin'] ?? []);
    }
}
