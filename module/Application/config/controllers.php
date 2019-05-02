<?php

use Application\Controller;
use Application\Controller\Factory;
use Application\Controller\Plugin;

return [
    'controllers'        => [
        'factories' => [
            Controller\IndexController::class         => Factory\IndexControllerFactory::class,
            Controller\AccountController::class       => Factory\AccountControllerFactory::class,
            Controller\AuthController::class          => Factory\AuthControllerFactory::class,
            Controller\CronController::class          => Factory\CronControllerFactory::class,
            Controller\EncryptionKeyController::class => Factory\EncryptionKeyControllerFactory::class,
            Controller\ExportController::class        => Factory\ExportControllerFactory::class,
            Controller\FolderController::class        => Factory\FolderControllerFactory::class,
            Controller\RegistrationController::class  => Factory\RegistrationControllerFactory::class,
            Controller\SearchController::class        => Factory\SearchControllerFactory::class,
            Controller\SettingsController::class      => Factory\SettingsControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'encryptionKeyCookie' => Plugin\EncryptionKeyCookiePlugin::class,
        ],
        'factories' => [
            Plugin\EncryptionKeyCookiePlugin::class => Plugin\Factory\EncryptionKeyCookiePluginFactory::class,
        ],
    ],
];
