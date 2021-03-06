<?php

namespace AceAdmin;

use AceDatagrid\Annotation\Title;
use Doctrine\Common\Annotations\AnnotationReader;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\ModuleManager\ModuleManager;

class Module
{
    public function getModuleDependencies()
    {
        return [
            'AssetManager',
            'DoctrineORMModule',
            'TwbsHelper',
            'AceDatagrid',
        ];
    }

    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'onMergeConfig']);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onMergeConfig(ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $config         = $configListener->getMergedConfig(false);

        if (isset($config['navigation']['default']) && isset($config['ace_admin']['entities'])) {
            $key = array_search('ace-admin', array_column($config['navigation']['default'], 'route'));

            if ($key !== null) {
                $reader = new AnnotationReader();
                $config['navigation']['default'][$key]['pages'] = [];

                foreach ($config['ace_admin']['entities'] as $entitySlug => $entityClassName) {
                    $title = $reader->getClassAnnotation(new \ReflectionClass($entityClassName), Title::class);

                    $config['navigation']['default'][$key]['pages'][] = [
                        'label'  => $title->plural,
                        'route'  => 'ace-admin/entity',
                        'action' => 'list',
                        'params' => ['entity' => $entitySlug],
                        'admin'  => $entityClassName,
                    ];
                }

                asort($config['navigation']['default'][$key]['pages']);
            }
        }

        $configListener->setMergedConfig($config);
    }
}
