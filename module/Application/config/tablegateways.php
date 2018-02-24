<?php

use Application\Model;

return [
    'tablegateways' => [
        'users'            => [
            'table_name'   => 'users',
            'entity_class' => Model\User::class
        ],
        'folders'          => [
            'table_name'   => 'folders',
            'entity_class' => Model\Folder::class
        ],
        'accounts'         => [
            'table_name'   => 'accounts',
            'entity_class' => Model\Account::class
        ],
        'auth_log_success' => [
            'table_name'   => 'auth_log_success',
            'entity_class' => Model\AuthLogSuccess::class
        ],
        'auth_log_failure' => [
            'table_name'   => 'auth_log_failure',
            'entity_class' => Model\AuthLogFailure::class
        ],
        'encryption_keys'  => [
            'table_name'   => 'encryption_keys',
            'entity_class' => Model\EncryptionKey::class
        ],
    ],
];
