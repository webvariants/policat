<?php

class poilcatFilterArray implements policatFilter {

  protected $data = array();

  public function __construct($data) {
    $this->data = (array) $data;
  }

  public function getValue($field) {
    return array_key_exists($field, $this->data) ? $this->data[$field] : null;
  }

}
