<?php
$driver = getenv('CONFIG_DB_DRIVER') ?: 'mysql';
if (!in_array($driver, ['mysql', 'pgsql'], true)) {
    throw new RuntimeException("Unsupported CONFIG_DB_DRIVER '$driver'");
}

$connection = function (string $host) use ($driver) {
    $config = [
        'adapter' => $driver,
        'host' => $host,
        'name' => getenv('CONFIG_DB_NAME'),
        'user' => getenv('CONFIG_DB_USER'),
        'pass' => trim(file_get_contents(getenv('CONFIG_DB_PASSWORD_FILE')), "\r\n"),
        'port' => getenv('CONFIG_DB_PORT') ?: ($driver === 'pgsql' ? '5432' : '3306'),
    ];
    if ($driver === 'mysql') {
        $config['charset'] = 'utf8';
        $config['collation'] = 'utf8mb4_0900_ai_ci';
    }
    return $config;
};

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => $connection(getenv('CONFIG_DB_HOST') ?: 'db'),
        'development' => $connection(getenv('CONFIG_DB_HOST') ?: 'db'),
        'testing' => $connection('localhost'),
    ],
    'version_order' => 'creation'
];
