<?php

class ValidatorWidget2Owner extends sfValidatorBase {

  const OPTION_WIDGET = 'widget';

  public function __construct($options = array(), $messages = array()) {
    $this->addRequiredOption(self::OPTION_WIDGET);
    parent::__construct($options, $messages);
  }


  protected function doClean($value) {
    if (empty($value)) throw new sfValidatorError($this, 'invalid');
    if (is_numeric($value)) {
      $widget = $this->getOption(self::OPTION_WIDGET);
      /* @var $widget Widget */

      if (!$widget->getWidgetOwner()->isNew()) throw new sfValidatorError($this, 'invalid');

      $owner = Doctrine_Core::getTable('Owner')
        ->createQuery('o')
        ->where('o.campaign_id = ?', $widget->getCampaignId())
        ->andWhere('o.first_widget_id = ?', $value)
        ->fetchOne();

      if ($owner) return $owner;
    }

    throw new sfValidatorError($this, 'invalid');
  }
}