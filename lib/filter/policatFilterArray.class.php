<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class poilcatFilterArray implements policatFilter {

  protected $data = array();

  public function __construct($data) {
    $this->data = (array) $data;
  }

  public function getValue($field) {
    return array_key_exists($field, $this->data) ? $this->data[$field] : null;
  }

}
