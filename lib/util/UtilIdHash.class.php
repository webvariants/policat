<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilIdHash
{
  const DEFAULT_SECRET = 'Oi9nsdsZMADSPMhsd8sdnsskwn320lbdw2js';

  protected $secret = null;
  protected $hash_function = 'md5';

  public function __construct($secret = null)
  {
    if ($secret === null) $this->secret = self::DEFAULT_SECRET;
    else if (is_scalar($secret)) $this->secret = $secret;
    else $this->secret = self::DEFAULT_SECRET;
  }

  protected function hash($value)
  {
    return call_user_func($this->hash_function, $value);
  }

  public function getHashById($id)
  {
    $hash_of_id = substr($this->hash($id . $this->secret), 16);
    $hash_key = substr($this->hash($hash_of_id . $this->secret . $id), 16);
    return "$id-$hash_of_id$hash_key";
  }

  public function getIdByHash($wannabe_hash)
  {
    if (!is_string($wannabe_hash)) return false;
    $parts = explode('-', $wannabe_hash, 2);
    
    if (!is_array($parts) || count($parts) != 2 || !is_numeric($parts[0])) return false;
    $id   = $parts[0];
    $hash = $parts[1];
    $hash_of_id = substr($hash, 0, 16);
    $hash_key_got = substr($hash, 16);
    $hash_key_expected = substr($this->hash($hash_of_id . $this->secret . $id), 16);
    return ($hash_key_expected == $hash_key_got) ? $id : false;
  }

  /**
   *
   * @param string $secret
   * @return UtilIdHash new object
   */
  public static function getInstance($secret = null)
  {
    return new self($secret);
  }
}