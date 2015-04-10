<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class OwnerAddForm extends BaseForm {
  const OPTION_WIDGET = 'widget';


  public function setup() {
    $widget = $this->getOption(self::OPTION_WIDGET);
    /* @var $widget Widget */
    if (!$widget) throw new Exception('missing widget');

      $this->widgetSchema->setFormFormatterName('policat');
    $this->getWidgetSchema()->setNameFormat('owner_append[%s]');

    $this->setWidget('widget_id', new sfWidgetFormInputText());
    $this->setValidator('widget_id', new ValidatorWidget2Owner(array(ValidatorWidget2Owner::OPTION_WIDGET => $widget)));
  }

  public function save() {
    $owner = $this->getValue('widget_id');
    /* @var $owner Owner */

    $widget = $this->getOption(self::OPTION_WIDGET);
    /* @var $widget Widget */

    $widget_owner = $widget->getWidgetOwner();;
    $widget_owner->setOwnerId($owner->getId());
    $widget_owner->save();
  }
}
