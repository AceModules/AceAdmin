<?php

namespace AceAdmin;

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
}
