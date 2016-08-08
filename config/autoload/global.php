<?php

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
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
    'session_config'  => [
        'name'            => 'sessid',
        'cookie_httponly' => true,
        'cookie_secure'   => true,
    ],
    'session_manager' => [
        'enable_default_container_manager' => true,
        'validators'                       => [
            'Zend\Session\Validator\HttpUserAgent',
            'Zend\Session\Validator\RemoteAddr'
        ]
    ],
    'application'     => [
        'enc_key_cookie' => [
            'name'     => 'encKey',
            'lifetime' => 1209600, // 2 weeks
            'secure'   => true,
        ],
        'registration'   => [
            'enabled' => false,
        ],
        'authentication' => [
            'logListener' => [
                'maxfailures' => 5,
                'blocktime'   => 60 * 60 * 24,
            ]
        ],
    ],
];

