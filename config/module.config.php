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
    'view_manager' => [
        'template_path_stack' => [
            'AceAdmin' => __DIR__ . '/../view',
        ],
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __NAMESPACE__ => __DIR__ . '/../public',
            ],
        ],
    ],
    'form_annotation_builder' => [
        'annotations' => [
            'Doctrine\ORM\Mapping\ManyToMany',
            'Doctrine\ORM\Mapping\ManyToOne',
            'Doctrine\ORM\Mapping\OneToMany',
            'Doctrine\ORM\Mapping\OneToOne',
        ],
        'listeners' => [
            'AceAdmin\Form\Element\ObjectElementListener',
        ],
    ],
];