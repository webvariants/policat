<?php

class WidgetBootstrapRadio extends sfWidgetFormChoice {

  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    if (!isset($options['renderer_class']))
      $this->setOption('renderer_class', 'WidgetFormSelectBootstrapRadio');
  }

}
