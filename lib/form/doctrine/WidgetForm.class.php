<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Widget form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class WidgetForm extends BaseWidgetForm {

  public function setup() {
    parent::setup();
    unset(
      $this['user_id'], $this['data_owner'], $this['created_at'], $this['updated_at'], $this['campaign_id'], $this['petition_id'], $this['petition_text_id'], $this['stylings'], $this['email'], $this['organisation'], $this['validation_kind'], $this['validation_data'], $this['validation_status'], $this['edit_code'], $this['object_version'], $this['parent_id'], $this['ref'], $this['paypal_email'], $this['activity_at'], $this['last_ref'], $this['donate_url'], $this['donate_text'], $this['themeId'], $this['email_targets']
    );
    $petition = $this->getObject()->getPetition();

    $petition_paypal_email = $petition->getPaypalEmail();
    if ((is_string($petition_paypal_email) && strpos($petition_paypal_email, '@')) || !$this instanceof WidgetPublicForm) {
      $this->setWidget('paypal_email', new WidgetFormInputInverseCheckbox(array('value_attribute_value' => 'ignore')));
      $this->setValidator('paypal_email', new ValidatorInverseCheckbox(array('value_attribute_value' => 'ignore')));
      $this->getWidgetSchema()->setLabel('paypal_email', 'Include fundraising form');
    }
  }

  protected function getWidthChoices() {
    $choices = array('auto' => 'auto');
    for ($i = 440; $i <= 740; $i++) {
      $choices[$i] = $i;
    }

    return $choices;
  }

  protected function getKeywords($glue = null) {
    $keywords = array();
    $petition = $this->getObject()->getPetition();
    if ($petition->isGeoKind()) {
      $subst_fields = $petition->getGeoSubstFields();
      foreach ($subst_fields as $keyword => $subst_field) {
        if ($subst_field['id'] != MailingList::FIX_GENDER) {
          $keywords[] = '<b>' . $keyword . '</b> (' . $subst_field['name'] . ')';
        }
      }
      foreach (PetitionSigningTable::$KEYWORDS as $keyword) {
        $keywords[] = $keyword;
      }

      $keywords[] = PetitionTable::KEYWORD_PERSONAL_SALUTATION;
    } else {
      $keywords = PetitionSigningTable::$KEYWORDS;
    }

    if ($glue !== null) {
      return implode($glue, $keywords);
    } else {
      return $keywords;
    }
  }

  protected function setWidgetDefaults() {
    $defaults_text = $this->getObject()->getPetitionText();
    $defaults_parent = null;
    if ($this->getObject()->getParentId()) {
      $defaults_parent = $this->getObject()->getParent();
    }
    if ($this->getObject()->isNew()) {
      foreach (array('title', 'target', 'background', 'intro', 'footer', 'email_subject', 'email_body') as $field) {
        if (isset($this[$field])) {
          if ($defaults_parent && $defaults_parent[$field]) {
            $this->setDefault($field, $defaults_parent[$field]);
          } else {
            $this->setDefault($field, $defaults_text[$field]);
          }
        }
      }
    }
  }

  protected function removeWidgetIndividualiseFields() {
    $petition = $this->getObject()->getPetition();
    if (!$petition->getWidgetIndividualiseText()) {
      foreach (array('title', 'target', 'background', 'intro', 'footer', 'email_subject', 'email_body') as $field) {
        if (isset($this[$field])) {
          unset($this[$field]);
        }
      }
    }
  }

  protected function doUpdateObject($values) {
    $stylings = $this->getObject()->getStylingsArray();
    foreach (array('type', 'width', 'title_color', 'body_color', 'button_color', 'bg_left_color', 'bg_right_color', 'form_title_color', 'button_primary_color', 'label_color', 'font_family') as $i) {
      if (array_key_exists('styling_' . $i, $values)) {
        $stylings[$i] = $values['styling_' . $i];
        unset($values['styling_' . $i]);
      }
    }
    $values['stylings'] = json_encode($stylings);

    if (!array_key_exists('type', $values)) {
      $values['type'] = 'embed';
    }

    if (!array_key_exists('width', $values)) {
      $values['width'] = 'auto';
    }

    parent::doUpdateObject($values);
  }

}
