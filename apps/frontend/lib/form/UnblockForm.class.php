<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UnblockForm extends BaseForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap4');
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