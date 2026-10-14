<?php

// Implemented by providers whose first step is a form that can sit directly on
// the login page rather than behind a button.
interface InlineLoginForm {
  public function render_inline_form(string $redirect): void;
}
