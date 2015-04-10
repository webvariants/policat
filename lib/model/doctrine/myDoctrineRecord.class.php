<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

abstract class myDoctrineRecord extends sfCachetaggableDoctrineRecord
{
  public function getCacheTag()
  {
    return array($this->getTagName() => $this->getObjectVersion());
  }

  protected function utilGetAsJsonArray($field)
  {
    $json = $this[$field];
    if (is_string($json) && strlen($json))
    {
      $array = json_decode($json, true);
      if (is_array($array)) {

        return $array;
      }
    }

    return array();
  }

  protected function utilSetAsJsonArray($field, $array)
  {
    $this[$field] = ((is_array($array) && $array) ? json_encode($array) : null);
  }

  protected function utilGetFieldFromArray($field, $name, $default = null)
  {
    $non_json_fields = $this->option('non_json_fields');
    $old_json_fields = $this->option('old_json_fields');

    if ($old_json_fields && isset($old_json_fields[$field]) && in_array($name, $old_json_fields[$field])) {
      $array = $this->utilGetAsJsonArray($field);
      if (array_key_exists($name, $array)) {
        return $array[$name];
      }
    }

    if (isset($non_json_fields[$field]) && in_array($name, $non_json_fields[$field]))
    {
      return $this[$name];
    }
    $array = $this->utilGetAsJsonArray($field);
    if (isset($array[$name])) {

      return $array[$name];
    }

    return $default;
  }

  protected function utilSetFieldFromArray($field, $name, $value)
  {
    $non_json_fields = $this->option('non_json_fields');
    $old_json_fields = $this->option('old_json_fields');

    if ($old_json_fields && isset($old_json_fields[$field]) && in_array($name, $old_json_fields[$field])) {
      $array = $this->utilGetAsJsonArray($field);
      if (array_key_exists($field, $array)) { // remove old entry from json
        unset($array[$field]);
        $this->utilSetAsJsonArray($field, $array);
      }

      return $this[$name] = $value;
    }

    if (isset($non_json_fields[$field]) && in_array($name, $non_json_fields[$field])) {
      return $this[$name] = $value;
    }
    $array = $this->utilGetAsJsonArray($field);
    $array[$name] = $value;
    $this->utilSetAsJsonArray($field, $array);
  }

  public function utilCalcPossibleStatusByPermissions($matrix, $permissions)
  {
    if (!isset($this['status'])) return array();
    if ($permissions instanceof sfOutputEscaper) $permissions = $permissions->getRawValue();
    if (!is_array($permissions)) $permissions = array();

    $status = $this->getStatus();
    $mapping = $matrix[$status];
    $possible_status = array();
    foreach ($mapping as $target_status => $required_permissions)
      if (count(array_intersect($permissions, $required_permissions))) $possible_status[] = $target_status;
    if (is_numeric($status)) $possible_status[] = $status;
    return array_unique($possible_status);
  }

  public function utilCalcIsEditableByPermission($rights, $permissions)
  {
    if (!isset($this['status'])) return false;
    if ($permissions instanceof sfOutputEscaper) $permissions = $permissions->getRawValue();
    if (!is_array($permissions)) return false;

    $status = $this->getStatus();
    $required_permissions = isset ($rights[$status]) ? $rights[$status] : array();
    if (count(array_intersect($permissions, $required_permissions))) return true;
    return false;
  }
}
