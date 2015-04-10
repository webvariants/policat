<?php

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