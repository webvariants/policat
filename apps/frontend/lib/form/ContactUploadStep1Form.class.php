<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ContactUploadStep1Form extends sfForm {

  const FILE_PATTERN = '/[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9a-z]{16}\.csv/';

  public function getSeparator() {
    return $this->getValue('separator');
  }

  public function setup() {
    parent::setup();

    $this->widgetSchema->setFormFormatterName('bootstrap4');
    $this->widgetSchema->setNameFormat('contact_upload1[%s]');

    $this->setWidget('separator', new sfWidgetFormInputText());
    $this->setValidator('separator', new sfValidatorString(array('min_length' => 1, 'max_length' => 1)));
    $this->getWidgetSchema()->setDefault('separator', ',');

    $this->setWidget('file', new sfWidgetFormInputFile());
    $this->setValidator('file', new sfValidatorFile(array('required' => true)));
  }

  public function save() {
    $file = $this->getValue('file');
    /* @var $file sfValidatedFile */
    $filename = self::randomFilename();
    $file->save(self::getDir($filename));

    return $filename;
  }

  static private function randomFilename() {
    $salt = '';
    while (strlen($salt) < 16) {
      $salt .= base_convert(mt_rand(), 10, 36);
    }
    $salt = substr($salt, 0, 16);

    return gmdate('Y-m-d') . '_' . $salt . '.csv';
  }

  public static function getDir($filename = null) {
    if ($filename !== null) {
      if (!preg_match(self::FILE_PATTERN, $filename)) {
        throw new Exception('Invalid filename of csv file.');
      }
    }
    $dir = sfConfig::get('sf_data_dir') . '/csv_uploads';
    @mkdir($dir, 0777, true);
    return $dir . ($filename ? '/' . $filename : '');
  }

}
