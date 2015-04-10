<?php

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
