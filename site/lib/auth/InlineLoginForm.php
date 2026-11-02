<?php

require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

// Implemented by providers whose first step is a form that can sit directly on
// the login page rather than behind a button.
interface InlineLoginForm {
  public function inline_form(string $redirect): Template;
}
