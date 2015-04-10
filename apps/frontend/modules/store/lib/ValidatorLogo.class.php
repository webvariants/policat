<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorLogo extends sfValidatorFile {

  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);

    $this->setOption('mime_categories', 'web_images');
    $this->setOption('path', sfConfig::get('sf_web_dir') . '/images/store');
  }

}
