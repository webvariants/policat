<?php
use_helper('I18N');
if (isset($form))
{
  $errors = array();
  foreach ($form->getErrorSchema() as  $field => $error) $errors[$field] = __($error->getMessage());

  if ($form->isValid())
  {
    if ($form instanceof PetitionSigningForm) {
      if ($form->getNoMails()) {
        $errors['extra'] = __("Attention: You've already taken part in this action (maybe on another website).");
      } elseif ($form->getSkipValidation()) {
        $errors['extra'] = __('Your action is confirmed. Use this moment to tell friends and family.');
      } else {
        $errors['extra'] = __("Attention: You will receive a confirmation email. Check your email inbox (and junk folder) now! To confirm your action, click the link given in the email.");
      }
    }
//    else if ($form instanceof TellyourForm)
//      $errors['extra'] = __("We have sent an email with information about this campaign to the address(es) specified.");
  }

  echo json_encode(array
  (
  'isValid' => $form->isValid(),
  'errors'  => $errors,
  'extra'   => isset($extra) ? $extra : null
  ));
}
else
  echo json_encode('Error: something went wrong.');