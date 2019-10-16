<?php

namespace AceAdmin;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Factory\AdminControllerFactory::class,
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
                            'route'    => '/:entity[/:action[/:id[/:version]]]',
                            'defaults' => [
                                'action'        => 'list',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __NAMESPACE__ => __DIR__ . '/../view',
        ],
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __NAMESPACE__ => __DIR__ . '/../asset',
            ],
        ],
    ],
];
