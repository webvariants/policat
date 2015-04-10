<?php

class TestmailForm extends BaseForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('testmail[%s]');

    $this->setWidget('from', new sfWidgetFormInputText());
    $this->setValidator('from', new ValidatorEmail(array('required' => false)));
    
    $this->setWidget('to', new sfWidgetFormInputText());
    $this->setValidator('to', new ValidatorEmail());

    $this->setWidget('subject', new sfWidgetFormInputText());
    $this->setValidator('subject', new sfValidatorString());
    $this->setDefault('subject', 'Testmail ' . gmdate(DATE_RSS));

    $this->setWidget('body', new sfWidgetFormTextarea());
    $this->setValidator('body', new sfValidatorString(array('min_length' => 1, 'max_length' => 500, 'required' => true)));
    $this->setDefault('body', "Testmail " . mt_rand(10000, 99999));
  }

}