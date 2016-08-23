<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Validiert Liste von Emails und Domains.
 */
class ValidatorVerifiedEmails extends sfValidatorString {

  protected function doClean($value) {
    $value = parent::doClean($value);
    $pattern = '/^(([^@\s]+)@)?((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';

    $list = preg_split('/\r\n|[\r\n]/', $value);
    $clean_list = array();

    foreach ($list as $email) {
      $email = trim($email);
      if (empty($email))
        continue;
      if (!preg_match($pattern, $email))
        throw new sfValidatorError($this, 'invalid', array('value' => $email));
      $clean_list[] = $email;
    }

    return implode("\n", $clean_list);
  }

}
