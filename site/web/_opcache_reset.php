<?php
/**
 * OPcache Reset Script
 *
 * This script clears PHP's opcache, which is necessary after deploying
 * new code via symlink switching to prevent cached files from old releases
 * causing duplicate class declarations.
 *
 * Security: Only accessible with the correct secret token.
 */

$config_file = parse_ini_file(getenv('CONFIG_SECRETS_FILE'), true);
$reset_token = $config_file['reset']['reset_token'] ?? '';
$provided_token = $_GET['token'] ?? '';
if ($provided_token !== $reset_token) {
    http_response_code(403);
    exit('Forbidden');
}

if (!function_exists('opcache_invalidate')) {
    http_response_code(200);
    echo "OPcache is not enabled\n";
    exit;
}

$base_path = $_GET['path'] ?? '';

if (empty($base_path)) {
    http_response_code(400);
    exit("Error: 'path' parameter is required\n");
}

// Validate that the path exists and is a directory
if (!is_dir($base_path)) {
    http_response_code(400);
    exit("Invalid path: $base_path\n");
}

// Recursively find and invalidate all PHP files
$invalidated_count = 0;
$failed_count = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base_path, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $file_path = $file->getRealPath();
        if (opcache_invalidate($file_path, true)) {
            $invalidated_count++;
        } else {
            $failed_count++;
        }
    }
}

// Also clear realpath cache
clearstatcache(true);

http_response_code(200);
echo "OPcache invalidation complete\n";
echo "Base path: $base_path\n";
echo "Files invalidated: $invalidated_count\n";
if ($failed_count > 0) {
    echo "Files failed: $failed_count\n";
}
echo "Realpath cache cleared\n";
