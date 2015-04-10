<?php

class ValidatorLogo extends sfValidatorFile {

  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);
    
    $this->setOption('mime_categories', 'web_images');
    $this->setOption('path', sfConfig::get('sf_web_dir') . '/images/store');
  }

}