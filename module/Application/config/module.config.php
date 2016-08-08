<?php

use Application\Controller\CronController;

return [
    'service_manager' => [
        'aliases'            => [
            'Zend\Authentication\AuthenticationService' => 'AuthService'
        ],
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Application\Service\TableGatewayAbstractFactory',
        ],
        'invokables'         => [
            'FaviconService' => 'Application\Service\FaviconService',
        ],
        'shared'             => [
            'BlockCipher' => false
        ],
    ],
    'view_manager'    => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
        'strategies'               => [
            'ViewJsonStrategy',
        ],
    ],
    // Placeholder for console routes
    'console'         => [
        'router' => [
            'routes' => [
                'cron-clearKeysTable'       => [
                    'options' => [
                        'route'    => 'cron clearKeysTable',
                        'defaults' => [
                            'controller' => CronController::class,
                            'action'     => 'clearKeysTable',
                        ],
                    ],
                ],
                'cron-clearLogFailureTable' => [
                    'options' => [
                        'route'    => 'cron clearLogFailureTable',
                        'defaults' => [
                            'controller' => CronController::class,
                            'action'     => 'clearLogFailureTable',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
