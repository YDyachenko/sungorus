<?php

use Application\Controller;
use Application\Controller\Factory;
use Application\Controller\Plugin\Factory as PluginFactory;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Controller\PluginManager;

return [
    'controllers'        => [
        'factories' => [
            Controller\IndexController::class        => Factory\IndexControllerFactory::class,
            Controller\AccountController::class      => Factory\AccountControllerFactory::class,
            Controller\AuthController::class         => Factory\AuthControllerFactory::class,
            Controller\CronController::class         => Factory\CronControllerFactory::class,
            Controller\ExportController::class       => Factory\ExportControllerFactory::class,
            Controller\FolderController::class       => Factory\FolderControllerFactory::class,
            Controller\RegistrationController::class => Factory\RegistrationControllerFactory::class,
            Controller\SearchController::class       => Factory\SearchControllerFactory::class,
        ]
    ],
    'controller_plugins' => [
        'aliases'   => [
            'CheckUserEncryptionKey' => Plugin\CheckUserEncryptionKey::class,
            'SetEncryptionKeyCookie' => Plugin\SetEncryptionKeyCookie::class,
        ],
        'factories' => [
            Plugin\CheckUserEncryptionKey::class => PluginFactory\CheckUserEncryptionKeyFactory::class,
            Plugin\SetEncryptionKeyCookie::class => PluginFactory\SetEncryptionKeyCookieFactory::class,
        ]
    ]
];
