<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Validate required keywords.
 */
class ValidatorKeywords extends sfValidatorString {

  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);

    $this->addRequiredOption('keywords');
    $this->addMessage('missing', 'Missing the following keywords: %keywords%');
  }

  protected function doClean($value) {
    $value = parent::doClean($value);

    $missing = array();
    foreach ((array) $this->getOption('keywords') as $keyword) {
      if (strpos($value, $keyword) === false)
        $missing[] = $keyword;
    }

    if ($missing)
      throw new sfValidatorError($this, 'missing', array('keywords' => implode(', ', $missing)));

    return $value;
  }

}
