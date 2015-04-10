<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Validiert List.
 */
class ValidatorList extends sfValidatorString
{
  protected function doClean($value)
  {
    $value = parent::doClean($value);

    $list = preg_split("#\n|\r#", $value);
    $clean_list = array();
    $clean_list_lower = array();

    foreach ($list as $entry)
    {
      $entry = trim($entry);
      if (empty ($entry)) continue;
      $lower = mb_strtolower($entry, 'utf-8');
      if (in_array($lower, $clean_list_lower, true)) continue;
      $clean_list[] = $entry;
      $clean_list_lower[] = mb_strtolower($entry, 'utf-8');
    }

    if (empty($clean_list)) throw new sfValidatorError($this, 'required');

    return $clean_list;
  }
}
