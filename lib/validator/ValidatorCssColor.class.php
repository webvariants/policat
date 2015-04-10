<?php

class ValidatorCssColor extends sfValidatorRegex
{
  public function __construct($options = array(), $messages = array())
  {
    if (!isset($options['pattern'])) $options['pattern'] = "/#[0-9abcdefABCDEF]{6}/";

    parent::__construct($options, $messages);
  }
}