(function() {
  "use strict";

  function validateField($field) {
    const $error = $field.parentElement.querySelector(":scope > .error-message");
    if (!$error) {
      return true;
    }
    const fieldLabel = $field.parentElement.querySelector(":scope > label").innerText;

    let msg;
    if ($field.validity.patternMismatch) {
      msg = fieldLabel + " does not match the required pattern";
    } else if ($field.validity.tooLong) {
      msg = fieldLabel + " must be shorter than " + $field.maxLength + " characters";
    } else if ($field.validity.tooShort) {
      msg = fieldLabel + " must be longer than " +  $field.minLength + " characters";
    } else if ($field.validity.rangeOverflow) {
      msg = fieldLabel + " be less than " + $field.max;
    } else if ($field.validity.rangeUnderflow) {
      msg = fieldLabel + " be greater than " + $field.min;
    } else if ($field.validity.typeMismatch) {
      msg = fieldLabel + " must be a " + $field.type;
    } else if ($field.validity.valueMissing) {
      msg = fieldLabel + " is required";
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
        setTimeout(() => {
          if (document.activeElement !== $field) {
            validateField($field);
          }
        }, 300);
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