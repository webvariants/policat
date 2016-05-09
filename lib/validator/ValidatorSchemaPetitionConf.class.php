<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorSchemaPetitionConf extends sfValidatorSchema {

  protected function doClean($values) {
    $size = 0;

    foreach (array('nametype' => array(
          Petition::NAMETYPE_SPLIT => 2,
          Petition::NAMETYPE_FULL => 1
      ), 'with_address' => array(
          0 => 0,
          '0' => 0,
          1 => 1,
          '1' => 1,
          2 => 2,
          '2' => 2
      ), 'with_country' => array(
          0 => 0,
          '0' => 0,
          1 => 1,
          '1' => 1
      ), 'with_comments' => array(
          0 => 0,
          '0' => 0,
          1 => 1,
          '1' => 2
      )
    ) as $field => $opt) {
      $size += $opt[$values[$field]];
    }

    $errorSchema = new sfValidatorErrorSchema($this);

    if ($size > 5) {
      $errorSchema->addError(new sfValidatorError($this, 'You have selected too many form fields.'), 'customise');
    }

    if (!$values['with_country'] && !$values['default_country'])
      $errorSchema->addError(new sfValidatorError($this, 'Required if you do not ask for country.'), 'default_country');

    if ($errorSchema->count())
      throw $errorSchema;

    return $values;
  }

}