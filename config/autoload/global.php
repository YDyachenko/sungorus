<?php

use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in her e that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return [
    'db'              => [
        'driver'         => 'Pdo',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
        'dsn'            => 'mysql:dbname=sungorus;host=db',
        'username'       => 'sungorus',
        'password'       => 'sungorus',
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
    'session_config'  => [
        'name'            => 'sessid',
        'cookie_httponly' => true,
        'cookie_secure'   => false,
    ],
    'session_manager' => [
        'enable_default_container_manager' => true,
        'validators'                       => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    'application'     => [
        'enc_key_cookie' => [
            'name'     => 'encKey',
            'lifetime' => 1209600, // 2 weeks
            'secure'   => false,
        ],
        'registration'   => [
            'enabled' => true,
        ],
        'authentication' => [
            'maxfailures' => 5,
            'blocktime'   => 60 * 60 * 24,
        ],
    ],
];
