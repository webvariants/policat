<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorCssColor extends sfValidatorRegex {

  public function __construct($options = array(), $messages = array()) {
    if (!isset($options['pattern']))
      $options['pattern'] = "/#[0-9abcdefABCDEF]{6}/";

    $this->addOption('min-luma', false);
    $this->addMessage('too-bright', 'This color is too bright.');

    parent::__construct($options, $messages);
  }

  public static function luma($hexCode) {
    $r = hexdec(substr($hexCode, 1, 2)) / 255;
    $g = hexdec(substr($hexCode, 3, 2)) / 255;
    $b = hexdec(substr($hexCode, 5, 2)) / 255;

    return (0.213 * $r + 0.715 * $g + 0.072 * $b);
  }

  protected function doClean($value) {
    $color = parent::doClean($value);

    $minLuma = $this->getOption('min-luma');
    if ($minLuma !== false) {
      if (self::luma($value) < $minLuma) {
        throw new sfValidatorError($this, 'too-bright');
      }
    }

    return $color;
  }

}
