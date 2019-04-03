<?php

namespace AceAdmin;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'ace-admin' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/admin',
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'entity' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/:entity[/:action[/:id]]',
                            'defaults' => [
                                'action'        => 'list',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'invokables' => [
            'DatagridManager' => Datagrid\DatagridManager::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'AceAdmin' => __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'sortControl' => View\Helper\SortControl::class,
        ],
    ],
];