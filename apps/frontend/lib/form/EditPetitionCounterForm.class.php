<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class EditPetitionCounterForm extends BasePetitionForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('edit_petition[%s]');

    $this->useFields(array('addnum', 'target_num'));

    $this->getWidgetSchema()->setLabel('addnum', 'Sign-on counter start');
    $this->getWidget('addnum')->setAttribute('class', 'add_popover');
    $this->getWidget('addnum')->setAttribute('data-content', 'Add the number of activists that have signed-on to your action in the streets or via another e-action tool. The number will be added to the live counter in all widgets of your e-action. Be honest :-)');

    $this->getWidgetSchema()->setLabel('target_num', 'Sign-on counter target');
    $this->getWidget('target_num')->setAttribute('class', 'add_popover');
    $this->getWidget('target_num')->setAttribute('data-content', 'Add your action target as the number of sign-ons that you want to achieve. If you keep "0" in this field, the counter in all widgets will automatically set a motivating target – not too low, not too high – and increase the target automatically to the next level, once a level is met. We recommend keeping "0"in this field to use the automatic target setting. It\'s a fun feature :-) ');
  }

}
