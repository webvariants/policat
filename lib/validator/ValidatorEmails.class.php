<?php
/**
 * Validiert Liste von Emails.
 */
class ValidatorEmails extends sfValidatorString
{
  protected function doClean($value)
  {
    $value = parent::doClean($value);
    $pattern = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';

    $list = preg_split("#[,\s\n\r]+#", $value);
    $clean_list = array();

    foreach ($list as $email)
    {
      $email = trim($email);
      if (empty ($email)) continue;
      if (!preg_match($pattern, $email)) throw new sfValidatorError($this, 'invalid', array('value' => $email));
      $clean_list[] = $email;
    }

    return $clean_list;
  }
}
