<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sfWidgetFormSelectPetitionTextHash extends sfWidgetFormSelect
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ($value && is_string($value)) $value = PetitionText::getHashForId($value);
    return parent::render($name, $value, $attributes, $errors);
  }
}