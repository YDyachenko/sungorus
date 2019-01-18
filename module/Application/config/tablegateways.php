<?php

use Application\Model;
use Application\Hydrator\AccountDataHydrator;

return [
    'tablegateways' => [
        'UsersTable'          => [
            'table_name'   => 'users',
            'entity_class' => Model\User::class
        ],
        'FoldersTable'        => [
            'table_name'   => 'folders',
            'entity_class' => Model\Folder::class
        ],
        'AccountsTable'       => [
            'table_name'   => 'accounts',
            'entity_class' => Model\Account::class
        ],
        'AuthLogSuccessTable' => [
            'table_name'   => 'auth_log_success',
            'entity_class' => Model\AuthLogSuccess::class
        ],
        'AuthLogFailureTable' => [
            'table_name'   => 'auth_log_failure',
            'entity_class' => Model\AuthLogFailure::class
        ],
        'EncryptionKeysTable' => [
            'table_name'   => 'encryption_keys',
            'entity_class' => Model\EncryptionKey::class
        ],
        'AccountsDataTable'   => [
            'table_name'    => 'accounts_data',
            'entity_class'  => Model\AccountData::class,
            'hydrator_name' => AccountDataHydrator::class
        ],
    ],
];
