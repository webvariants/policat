<?php
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
