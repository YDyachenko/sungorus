<?php

use Application\Model;

return [
    'tablegateways' => [
        'users'            => [
            'table_name'   => 'users',
            'entity_class' => Model\UserEntity::class
        ],
        'folders'          => [
            'table_name'   => 'folders',
            'entity_class' => Model\FolderEntity::class
        ],
        'accounts'         => [
            'table_name'   => 'accounts',
            'entity_class' => Model\AccountEntity::class
        ],
        'auth_log_success' => [
            'table_name'   => 'auth_log_success',
            'entity_class' => Model\AuthLogSuccessEntity::class
        ],
        'auth_log_failure' => [
            'table_name'   => 'auth_log_failure',
            'entity_class' => Model\AuthLogFailureEntity::class
        ],
        'encryption_keys'  => [
            'table_name'   => 'encryption_keys',
            'entity_class' => Model\EncryptionKeyEntity::class
        ],
    ],
];
