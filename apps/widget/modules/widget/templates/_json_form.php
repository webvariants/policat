<?php
use_helper('I18N');
if (isset($form))
{
  $errors = array();
  foreach ($form->getErrorSchema() as  $field => $error) {
    $errors[$field] = array('message' => __($error->getMessage()), 'code' => $error->getCode());
  }

  if ($form->isValid())
  {
    if ($form instanceof PetitionSigningForm) {
      $petition = $form->getObject()->getPetition();
      if ($form->getNoMails()) {
        // no targets on EMAIL-TO-LIST or PLEDGE action
        $errors['extra'] = array('code' => 'already', 'message' => __("Attention: You've already taken part in this action (maybe on another website)."));
      } elseif ($form->getSkipValidation()) {
        if ($petition->getKind() == Petition::KIND_OPENECI) {
        } else {
          $errors['extra'] = array('code' => 'confirmed', 'message' => __('Your action is confirmed.') . ' ' . __('Use this moment to tell friends and family.'));
        }

      } else {
        $errors['extra'] = array('code' => 'confirmation', 'message' => __("Attention: You will receive a confirmation email. Check your email inbox (and junk folder) now! To confirm your action, click the link given in the email."));
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
else {
  echo json_encode('Error: something went wrong.');
}