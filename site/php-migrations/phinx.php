<?php
return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'db',
            'name' => getenv('CONFIG_DB_NAME'),
            'user' => getenv('CONFIG_DB_USER'),
            'pass' => file_get_contents(getenv('CONFIG_DB_PASSWORD_FILE')),
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'db',
            'name' => getenv('CONFIG_DB_NAME'),
            'user' => getenv('CONFIG_DB_USER'),
            'pass' => file_get_contents(getenv('CONFIG_DB_PASSWORD_FILE')),
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => getenv('CONFIG_DB_NAME'),
            'user' => getenv('CONFIG_DB_USER'),
            'pass' => file_get_contents(getenv('CONFIG_DB_PASSWORD_FILE')),
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
