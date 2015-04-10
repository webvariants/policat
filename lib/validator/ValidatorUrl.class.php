<?php

class ValidatorUrl extends sfValidatorRegex
{
  public function __construct($options = array(), $messages = array())
  {
    if (!isset($options['pattern'])) $options['pattern'] = "#https?://[^\"\s]+\.[^\"\s]+#";

    parent::__construct($options, $messages);
  }
}