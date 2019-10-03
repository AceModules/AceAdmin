<?php

namespace AceAdmin;

use Zend\ModuleManager\ModuleManager;

class Module
{
    public function getModuleDependencies()
    {
        return [
            'AssetManager',
            'DoctrineORMModule',
            'TwbBundle',
            'AceDatagrid',
        ];
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }

    public function init(ModuleManager $moduleManager)
    {
        $moduleManager->loadModule('Ace\\Datagrid');
    }
}
