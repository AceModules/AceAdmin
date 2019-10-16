<?php

namespace AceAdmin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Factory\AdminControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'ace-admin' => [
                'type'    => Literal::class,
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
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/:entity[/:action[/:id[/:version]]]',
                            'defaults' => [
                                'action'        => 'list',
                            ],
                            'constraints' => [
                                'action'        => '(add|edit|delete|history|revert|suggest)',
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
    'form_elements' => [
        'aliases' => [
            'objectlivesearch' => Form\Element\ObjectLiveSearch::class,
        ],
        'factories' => [
            Form\Element\ObjectLiveSearch::class => Factory\ObjectLiveSearchFactory::class,
        ],
    ],
];
