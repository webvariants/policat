<?php

class MailExportSettingForm extends BaseForm {

  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null) {
    parent::__construct($defaults, $options, $CSRFSecret);
  }

  public function setup() {
    foreach (MailExport::getServices() as $service) {
      $service->formSetup($this->getOption('petition'), $this);
    }

    $this->widgetSchema->setNameFormat('mailexport_setting[%s]');
    $this->getWidgetSchema()->setFormFormatterName('bootstrap');
  }
}