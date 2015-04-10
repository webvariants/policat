<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @method Owner getObject()
 */
class OwnerStatusForm extends BaseFormDoctrine {
  public function setup() {
    $this->widgetSchema->setFormFormatterName('policat');
    $this->getWidgetSchema()->setNameFormat('widget_owner_status[%s]');

    $this->setWidget('status', new sfWidgetFormChoice(array('choices' => Owner::$STATUS_SHOW, 'expanded' => true)));
    $this->setValidator('status', new sfValidatorChoice(array('choices' => array_keys(Owner::$STATUS_SHOW))));

    $this->setValidator('stats', new sfValidatorPass());

    $this->setWidget('send_email', new WidgetFormInputCheckbox(array('value_attribute_value' => 1)));
    $this->setValidator('send_email', new sfValidatorChoice(array('choices' => array('1'), 'required' => false)));
  }

  public function getModelName()
  {
    return 'Owner';
  }

  protected function doSave($con = null) {
    $stats = $this->getValue('stats');
    if (is_array($stats)) {
      foreach ($this->getObject()->getWidgetOwner() as $widget_owner) {
        /* @var $widget_owner WidgetOwner */
        if (array_key_exists($widget_owner->getId(), $stats)) {
          $new_stat = $stats[$widget_owner->getId()];
          if (array_key_exists($new_stat, Owner::$STATUS_SHOW) && $new_stat != $widget_owner->getStatus()) {
            $widget_owner->setStatus($new_stat);
          }
        }
      }
    }
    parent::doSave($con);

    if ($this->getValue('send_email')) {
      list($subject, $body) = DefaultText::fetchText(DefaultText::TEXT_AGREEMENT_REPLY);
      $body = UtilMarkdown::transform($body);
      $owner = $this->getObject();
      UtilMail::send(null, null, array($owner->getEmail() => $owner->getFirstname()
        . ' ' . $owner->getLastname()), $subject, $body, 'text/html');
    }
  }
}
