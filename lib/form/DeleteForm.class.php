<?php

class DeleteForm extends BaseForm {
  static $foobar = 0;

  public function setup() {
    $this->widgetSchema->setFormFormatterName('policat');

    $this->getWidgetSchema()->setNameFormat('delete[%s]');
    $this->setWidget('sure', new sfWidgetFormChoice(array('label' => 'Are you sure?', 'choices' => array('' => '', 'yes' => 'yes'))));
    $this->setValidator('sure', new sfValidatorChoice(array('choices' => array('yes'))));
  }
}