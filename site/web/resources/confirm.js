// Asks before submitting a form with a data-confirm-title, in a dialog that
// shows what will happen and to whom, which is harder to click through than
// confirm(). The attributes can instead be on a submit button, for a form
// with several. They give the text:
//
//   data-confirm-title     the question
//   data-confirm-details   JSON object of label => value, shown as a list
//   data-confirm-message   optional, shown under the list
//   data-confirm-button    the text of the button that submits
(function() {
  "use strict";

  function element(tag, className, text) {
    const $el = document.createElement(tag);
    if (className) {
      $el.className = className;
    }
    if (text !== undefined) {
      $el.textContent = text;
    }
    return $el;
  }

  const $dialog = element('dialog', 'confirm');
  const $title = element('p');
  $title.id = 'confirm-title';
  $title.style.fontWeight = 'bold';
  const $details = element('dl');
  const $message = element('p');
  const $buttons = element('div', 'buttons');
  const $cancel = element('button', 'button link', 'Cancel');
  $cancel.type = 'button';
  const $ok = element('button', 'button');
  $ok.type = 'button';
  $buttons.append($cancel, $ok);
  $dialog.append($title, $details, $message, $buttons);
  $dialog.setAttribute('aria-labelledby', 'confirm-title');

  let $pendingForm = null;
  let $pendingSubmitter = null;

  function close() {
    $pendingForm = null;
    $pendingSubmitter = null;
    $dialog.close();
  }

  function ask($form, $submitter, data) {
    $pendingForm = $form;
    $pendingSubmitter = $submitter;
    $title.textContent = data.confirmTitle;
    $details.replaceChildren();
    const details = JSON.parse(data.confirmDetails || '{}');
    for (const label of Object.keys(details)) {
      $details.append(element('dt', null, label), element('dd', null, details[label]));
    }
    $message.textContent = data.confirmMessage || '';
    $message.hidden = !data.confirmMessage;
    $ok.textContent = data.confirmButton || 'OK';
    $dialog.showModal();
    $cancel.focus();
  }

  $cancel.addEventListener('click', close);
  $ok.addEventListener('click', function() {
    const $form = $pendingForm;
    const $submitter = $pendingSubmitter;
    close();
    if ($form) {
      // submit() doesn't send the button's own name and value, so add them
      if ($submitter && $submitter.name) {
        const $input = element('input');
        $input.type = 'hidden';
        $input.name = $submitter.name;
        $input.value = $submitter.value;
        $form.append($input);
      }
      // submit() skips the submit event, so this doesn't ask again
      $form.submit();
    }
  });
  $dialog.addEventListener('click', function(event) {
    if (event.target === $dialog) {
      close();
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    document.body.append($dialog);
  });

  document.addEventListener('submit', function(event) {
    const $form = event.target;
    const $submitter = event.submitter;
    const source = $submitter && $submitter.dataset.confirmTitle ? $submitter : $form;
    if (!source.dataset.confirmTitle) {
      return;
    }
    event.preventDefault();
    ask($form, $submitter, source.dataset);
  });
})();
