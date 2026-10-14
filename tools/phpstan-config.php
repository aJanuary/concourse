<?php
// Every constant config.php defines is a per-convention or per-deployment
// setting, so PHPStan treats them by their types, not the values in this
// checkout. Otherwise it reports code for settings that are off here as dead.
// A literal array is typed as just an array, as its shape is the
// convention's; code that reads one says what it expects, like
// business_meeting_mpc_election().
$tokens = array_values(array_filter(
  token_get_all(file_get_contents(__DIR__ . '/../site/lib/config.php')),
  fn($token) => !is_array($token) || !in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)
));
$names = [];
foreach ($tokens as $i => $token) {
  if (is_array($token) && $token[0] === T_STRING && strtolower($token[1]) === 'define'
      && $tokens[$i + 1] === '(' && $tokens[$i + 2][0] === T_CONSTANT_ENCAPSED_STRING) {
    $name = trim($tokens[$i + 2][1], '\'"');
    if ($tokens[$i + 4] === '[') {
      $names[$name] = 'array';
    } else {
      $names[] = $name;
    }
  }
}
return ['parameters' => ['dynamicConstantNames' => $names]];
