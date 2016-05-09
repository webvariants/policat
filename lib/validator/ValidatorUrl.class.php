<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorUrl extends sfValidatorRegex
{
  public function __construct($options = array(), $messages = array())
  {
    if (!isset($options['pattern'])) $options['pattern'] = "#https?://[^\"\s]+\.[^\"\s]+#";

    parent::__construct($options, $messages);
  }
}