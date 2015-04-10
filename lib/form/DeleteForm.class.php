<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class DeleteForm extends BaseForm {
  static $foobar = 0;

  public function setup() {
    $this->widgetSchema->setFormFormatterName('policat');

    $this->getWidgetSchema()->setNameFormat('delete[%s]');
    $this->setWidget('sure', new sfWidgetFormChoice(array('label' => 'Are you sure?', 'choices' => array('' => '', 'yes' => 'yes'))));
    $this->setValidator('sure', new sfValidatorChoice(array('choices' => array('yes'))));
  }
}
