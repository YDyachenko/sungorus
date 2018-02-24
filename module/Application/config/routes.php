<?php

use Application\Controller\AccountController;
use Application\Controller\AuthController;
use Application\Controller\ExportController;
use Application\Controller\FolderController;
use Application\Controller\IndexController;
use Application\Controller\RegistrationController;
use Application\Controller\SearchController;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'home'          => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'auth'          => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/auth/[:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults'    => [
                        'controller' => AuthController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'login'         => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'export'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/export',
                    'defaults' => [
                        'controller' => ExportController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'signup'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/signup',
                    'defaults' => [
                        'controller' => RegistrationController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'encryptionKey' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/encryptionKey',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'encryptionKey',
                    ],
                ],
            ],
            'search'         => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/search',
                    'defaults' => [
                        'controller' => SearchController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'addFolder' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/folder/add',
                    'defaults' => [
                        'controller' => FolderController::class,
                        'action'     => 'add',
                    ],
                ],
            ],
            'folder'        => [
                'type'          => Segment::class,
                'options'       => [
                    'route'       => '/folder/:folderId',
                    'constraints' => [
                        'folderId' => '\d+',
                    ],
                    'defaults'    => [
                        'controller' => FolderController::class,
                        'action'     => 'view',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'edit'       => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/edit',
                            'defaults' => [
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'delete'     => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/delete',
                            'defaults' => [
                                'action' => 'delete',
                            ],
                        ],
                    ],
                    'addAccount' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'controller' => AccountController::class,
                                'action'     => 'add',
                            ],
                        ],
                    ],
                    'account'    => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'       => '/:accountId',
                            'constraints' => [
                                'accountId' => '\d+',
                            ],
                            'defaults'    => [
                                'controller' => AccountController::class,
                                'action'     => 'edit',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'favicon' => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/favicon',
                                    'defaults' => [
                                        'action' => 'favicon',
                                    ],
                                ],
                            ],
                            'delete'  => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/delete',
                                    'defaults' => [
                                        'action' => 'delete',
                                    ],
                                ],
                            ],
                            'openUrl' => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/open-url',
                                    'defaults' => [
                                        'action' => 'openUrl',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]
    ]
];
