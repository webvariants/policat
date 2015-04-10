<?php

class WidgetLogo extends sfWidgetFormInputFileEditable {

  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);

    $this->setOption('file_src', '/images/store');
    $this->setOption('is_image', true);
    $this->setOption('with_delete', false);
    $this->setOption('template', '<div>%file%<br />%input%<br />%delete% %delete_label%</div>');
  }

}