(function() {
  "use strict";

  function validateField($field) {
    const $error = $field.parentElement.querySelector(":scope > .error-message");
    if (!$error) {
      return true;
    }

    let msg;
    if ($field.validity.patternMismatch) {
      msg = "Value does not match the required pattern";
    } else if ($field.validity.tooLong) {
      msg = "Must be shorter than " + $field.maxlength + " characters";
    } else if ($field.validity.tooShort) {
      msg = "Must be longer than " +  $field.minlength + " characters";
    } else if ($field.validity.rangeOverflow) {
      msg = "Must be less than " + $field.max;
    } else if ($field.validity.rangeUnderflow) {
      msg = "Must be greater than " + $field.min;
    } else if ($field.validity.typeMismatch) {
      msg = "Must be a " + $field.type;
    } else if ($field.validity.valueMissing) {
      msg = "Field is required";
    } else if (!$field.validity.valid) {
      msg = "Invalid value";
    } else {
      msg = "";
    }

    $error.textContent = msg;
    return msg === "";
  }

  document.querySelectorAll(".nu-form").forEach($form => {
    $form.setAttribute("novalidate", true);

    const $fields = $form.querySelectorAll("input, textarea");

    $fields.forEach($field => {
      $field.addEventListener("blur", e => {
        validateField($field);
      });
    });

    $form.addEventListener("submit", e => {
      e.preventDefault();

      let isValid = true;
      $fields.forEach($field => {
        if (!validateField($field)) {
          isValid = false;
        }
      });

      if (isValid) {
        $form.submit();
      } else {
        $form.querySelector(":invalid").focus();
      }
    });
  });
})();