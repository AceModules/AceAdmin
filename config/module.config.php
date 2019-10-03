<?php

namespace AceAdmin;

use Doctrine\ORM\Mapping;

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
    'form_annotation_builder' => [
        'annotations' => [
            Mapping\ManyToMany::class,
            Mapping\ManyToOne::class,
            Mapping\OneToMany::class,
            Mapping\OneToOne::class,
        ],
        'listeners' => [
            Form\Element\ObjectElementListener::class,
        ],
    ],
];
