<?php
// Compiles every template in site/lib/templates with the portal's Twig
// environment, so a syntax error or an unknown function, filter or test fails
// the check rather than a page. A missing variable only shows when a page
// renders; development's strict_variables catches those. Run by scripts/lint.
$_SERVER['HTTP_HOST'] ??= 'localhost';
$_SERVER['REQUEST_URI'] ??= '/';
putenv('RUNTIME_ENV=development');
require_once getenv('CONFIG_LIB_DIR') . '/twig.php';

$twig = twig();
$root = getenv('CONFIG_LIB_DIR') . '/templates';
$failed = 0;
$count = 0;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($files as $file) {
  if (!str_ends_with($file->getFilename(), '.twig')) {
    continue;
  }
  $name = substr($file->getPathname(), strlen($root) + 1);
  $count++;
  try {
    $twig->compileSource($twig->getLoader()->getSourceContext($name));
  } catch (\Twig\Error\Error $e) {
    $failed++;
    fwrite(STDERR, "$name: " . $e->getMessage() . "\n");
  }
}
echo "Twig: $count templates, $failed with errors\n";
exit($failed ? 1 : 0);
