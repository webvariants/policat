<?php

class UnblockForm extends BaseForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('unblock[%s]');

    $this->setWidget('reason', new sfWidgetFormTextarea(
        array('label' => 'Reason'),
        array('class' => 'span5', 'placeholder' => 'Explain why you should be unblocked', 'style' => 'height: 160px'))
    );

    $this->setValidator('reason', new sfValidatorString(array(
          'min_length' => 30,
          'max_length' => 10000,
          'required' => true
      )));
  }

}