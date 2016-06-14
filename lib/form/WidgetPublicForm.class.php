<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetPublicForm extends WidgetForm {

  protected $state_count = true;
  protected $one_side = false;

  public function isOneSide() {
    return $this->one_side;
  }

  public function configure() {
    $this->getWidgetSchema()->setFormFormatterName('policat');
    $petition = $this->getObject()->getPetition();
    $this->getObject()->setStatus(Widget::STATUS_ACTIVE);

    unset($this['id'], $this['status'], $this['origin_widget_id'], $this['share']);

    $parent = $this->getObject()->getParentId() ? $this->getObject()->getParent() : null;

    $this->setWidget('title', new sfWidgetFormInput(array(), array('size' => 90)));

    $this->setWidget('styling_type', new sfWidgetFormChoice(array('choices' => array('popup' => 'Popup', 'embed' => 'Embed'))));
    $this->setValidator('styling_type', new sfValidatorChoice(array('choices' => array('popup', 'embed'))));
    $this->setDefault('styling_type', $this->getObject()->getStyling('type', 'embed'));
    $this->getWidgetSchema()->setLabel('styling_type', 'Widget type');

    $choices = $this->getWidthChoices();
    $this->setWidget('styling_width', new sfWidgetFormChoice(array('choices' => $choices)));
    $this->setValidator('styling_width', new sfValidatorChoice(array('choices' => array_keys($choices))));
    $this->setDefault('styling_width', $this->getObject()->getStyling('width', 'auto'));
    $this->getWidgetSchema()->setLabel('styling_width', 'Width');

    if ($petition->getWidgetIndividualiseDesign()) {
      $this->setWidget('styling_title_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_title_color', $this->getObject()->getStyling('title_color', $parent ? $parent->getStyling('title_color') : '#181716'));
      $this->getWidgetSchema()->setLabel('styling_title_color', 'Text title');

      $this->setWidget('styling_body_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_body_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_body_color', $this->getObject()->getStyling('body_color', $parent ? $parent->getStyling('body_color') : '#666666'));
      $this->getWidgetSchema()->setLabel('styling_body_color', 'Text body');

      $this->setWidget('styling_bg_left_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_bg_left_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_bg_left_color', $this->getObject()->getStyling('bg_left_color', $parent ? $parent->getStyling('bg_left_color') : '#e5e5e5'));
      $this->getWidgetSchema()->setLabel('styling_bg_left_color', 'Backgr left');

      $this->setWidget('styling_bg_right_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_bg_right_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_bg_right_color', $this->getObject()->getStyling('bg_right_color', $parent ? $parent->getStyling('bg_right_color') : '#f2f2f2'));
      $this->getWidgetSchema()->setLabel('styling_bg_right_color', 'Backgr right');

      $this->setWidget('styling_form_title_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_form_title_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_form_title_color', $this->getObject()->getStyling('form_title_color', $parent ? $parent->getStyling('form_title_color') : '#181716'));
      $this->getWidgetSchema()->setLabel('styling_form_title_color', 'Form title');

      $this->setWidget('styling_button_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_button_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_button_color', $this->getObject()->getStyling('button_color', $parent ? $parent->getStyling('button_color') : '#76b235'));
      $this->getWidgetSchema()->setLabel('styling_button_color', 'Button');

      $this->setWidget('styling_button_primary_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_button_primary_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_button_primary_color', $this->getObject()->getStyling('button_primary_color', $parent ? $parent->getStyling('button_primary_color') : '#76b235'));
      $this->getWidgetSchema()->setLabel('styling_button_primary_color', 'Sign Button');

      $this->setWidget('styling_label_color', new sfWidgetFormInput(array(), array('class' => 'color {hash:true}')));
      $this->setValidator('styling_label_color', new ValidatorCssColor(array('min_length' => 7, 'max_length' => 7)));
      $this->setDefault('styling_label_color', $this->getObject()->getStyling('label_color', $parent ? $parent->getStyling('label_color') : '#666666'));
      $this->getWidgetSchema()->setLabel('styling_label_color', 'Form label');

      $this->setWidget('styling_font_family', new sfWidgetFormChoice(array('choices' => UtilFont::formOptions('default'), 'label' => 'Font')));
      $this->setValidator('styling_font_family', new sfValidatorChoice(array('choices' => UtilFont::$FONTS, 'required' => false)));
      $this->setDefault('styling_font_family', $this->getObject()->getStyling('font_family', $parent ? $parent->getStyling('font_family') : ''));
    }

    $this->setWidget('target', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 3)));

    $this->setWidget('background', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5)));
    if (!$petition->isEmailKind()) {
      $this->setWidget('intro', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5)));
      $this->setWidget('footer', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5)));
      $this->getValidator('intro')->setOption('required', false);
      $this->getValidator('footer')->setOption('required', false);
      unset($this['email_subject'], $this['email_body']);
    } else {
      if ($petition->getKind() == Petition::KIND_PLEDGE) {
        unset($this['email_subject'], $this['email_body']);
      } else {
        $this->setWidget('email_subject', new sfWidgetFormInput(array(), array('size' => 90)));
        $this->setWidget('email_body', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 5)));
        $this->getValidator('email_subject')->setOption('required', true);
        $this->getValidator('email_body')->setOption('required', true);
      }
      unset($this['intro'], $this['footer']);
      if ($petition->getKind() != Petition::KIND_PLEDGE) {
        $this->getWidgetSchema()->setHelp('email_subject', 'You can use the following keywords: ' . $this->getKeywords(', ') . '.');
      }
    }

    $this->setWidgetDefaults();
    $this->removeWidgetIndividualiseFields();

    $widget = $this->getObject();

    $this->setWidget('edit_code', new sfWidgetFormInputHidden());
    $this->setValidator('edit_code', new sfValidatorString(array('required' => false)));

    $this->setWidget('petition_text_id', new WidgetPetitionText(array(
        'petition_id' => $widget->getPetitionId(),
        'petition_text_id' => $widget->getPetitionTextId()
    )));
    $this->setValidator('petition_text_id', new ValidatorPetitionText(array(
        'petition_id' => $widget->getPetitionId(),
        'petition_text_id' => $widget->getPetitionTextId(),
        'required' => false
    )));
    $this->getWidgetSchema()->setLabel('petition_text_id', false);

    if ($widget->isNew()) {
      $this->setWidget('email', new sfWidgetFormInput());
      $this->setValidator('email', new ValidatorEmail());
      $this->getWidgetSchema()->setLabel('email', 'Email address');

      $this->setWidget('organisation', new sfWidgetFormInput());
      $this->setValidator('organisation', new sfValidatorString());
    }

    if (isset($this['title'])) {
      $this->setWidget('title', new sfWidgetFormInputHidden(array(), array('class' => 'original')));
    }
    if (isset($this['target'])) {
      $this->setWidget('target', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original not_required')));
    }
    if (isset($this['background'])) {
      $this->setWidget('background', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));
    }
    if (isset($this['intro'])) {
      $this->setWidget('intro', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));
    }
    if (isset($this['footer'])) {
      $this->setWidget('footer', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));
    }
    if (isset($this['email_subject'])) {
      $this->setWidget('email_subject', new sfWidgetFormInputHidden(array(), array('class' => 'original')));
    }
    if (isset($this['email_body'])) {
      $this->setWidget('email_body', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));
    }

    $this->setWidget('ref', new sfWidgetFormInputHidden(array(), array('class' => 'ref')));
    $this->setValidator('ref', new sfValidatorString(array('required' => false)));

    if ($petition->getWidgetIndividualiseText() || $petition->getWidgetIndividualiseDesign()) {
      $this->setWidget('landing_url', new sfWidgetFormInputHidden(array(), array('class' => 'original not_required')));
    } else {
      $this->setWidget('landing_url', new sfWidgetFormInputText(array(
          'label' => 'Email Validation Landingpage - auto forwarding to external page',
          'default' => $widget->getInheritLandingUrl()
        ), array(
          'class' => 'url not_required',
          'placeholder' => 'http://example.com/'
      )));
      $this->one_side = true;
    }

    $this->setValidator('landing_url', new ValidatorUrl(array('required' => false, 'trim' => true)));

    $this->getWidgetSchema()->setFormFormatterName('policatWidget');
  }

  public static function utilPosition($array, $key1, $key2) {
    if (in_array($key1, $array) && in_array($key1, $array)) {
      foreach ($array as $key) {
        if ($key === $key1) {
          return 2;
        }
        if ($key === $key2) {
          return true;
        }
      }
      return true;
    }
    return false;
  }

  public function isGroupedField($name) {
    $fieldNames = array('styling_type', 'styling_width', 'styling_title_color', 'styling_body_color', 'styling_bg_left_color', 'styling_bg_right_color', 'styling_form_title_color', 'styling_button_color', 'styling_button_primary_color', 'styling_label_color');
    if (in_array($name, $fieldNames)) {
      switch ($name) {
        case 'styling_type': return self::utilPosition($fieldNames, 'styling_type', 'styling_width');
        case 'styling_width': return self::utilPosition($fieldNames, 'styling_width', 'styling_type');

        case 'styling_title_color': return self::utilPosition($fieldNames, 'styling_title_color', 'styling_body_color');
        case 'styling_body_color': return self::utilPosition($fieldNames, 'styling_body_color', 'styling_title_color');

        case 'styling_bg_left_color': return self::utilPosition($fieldNames, 'styling_bg_left_color', 'styling_bg_right_color');
        case 'styling_bg_right_color': return self::utilPosition($fieldNames, 'styling_bg_right_color', 'styling_bg_left_color');

        case 'styling_form_title_color': return self::utilPosition($fieldNames, 'styling_form_title_color', 'styling_button_color');
        case 'styling_button_color': return self::utilPosition($fieldNames, 'styling_button_color', 'styling_form_title_color');

        case 'styling_label_color': return self::utilPosition($fieldNames, 'styling_label_color', 'styling_button_primary_color');
        case 'styling_button_primary_color': return self::utilPosition($fieldNames, 'styling_button_primary_color', 'styling_label_color');
      }
    }
    return false;
  }

  protected function doUpdateObject($values) {
    if ($this->getObject()->isNew()) {
      $values['validation_data'] = Widget::genCode();
      $values['validation_kind'] = Widget::VALIDATION_KIND_EMAIL;
      $values['validation_status'] = Widget::VALIDATION_STATUS_PENDING;
      $values['edit_code'] = Widget::genCode();
      $values['donate_text'] = $this->getDefaultDonationText();

      $user = sfGuardUserTable::getInstance()->retrieveByUsername($values['email']);
      if ($user) {
        $values['user_id'] = $user->getId();
        $values['validation_status'] = Widget::VALIDATION_STATUS_VERIFIED;
      }

      $parent = $this->getObject()->getParentId() ? $this->getObject()->getParent() : null;
      if ($parent) {
        $values['share'] = $parent->getShare();
      }
    }

    parent::doUpdateObject($values);
  }

  private function getDefaultDonationText() {
    $widget = $this->getObject();
    $petition = $widget->getPetition();

    $text = null;
    if ($petition->getDonateUrl() && $petition->getDonateWidgetEdit()) {
      $text = $widget->getPetitionText()->getDonateText();
    }

    return $text;
  }

  protected function doSave($con = null) {
    $wasNew = $this->getObject()->isNew();
    parent::doSave($con);

    if ($wasNew) {
      $widget = $this->getObject();
      $petition = $widget->getPetition();
      $petition_text = $widget->getPetitionText();
      $subject = 'Validate your widget';
      $body = "Validate: #VALIDATION-URL#\nEdit: #EDIT-URL#";

      $store = StoreTable::getInstance()->findByKeyAndLanguageWithFallback(StoreTable::EMBED_WIDGET_MAIL, $petition_text->getLanguageId());
      if ($store) {
        $subject = $store->getField('subject');
        $body = $store->getField('body');
      }

      $validation = UtilLink::widgetValidation($this->getObject()->getId(), $this->getObject()->getValidationData());
      $edit_code = UtilLink::widgetEdit($this->getObject()->getId(), $this->getObject()->getEditCode());

      if ($widget->getUserId()) {
        $validation = 'Your widget is linked to the user with the e-mail-address "' . $widget->getEmail() . '".';
        $edit_code = sfContext::getInstance()->getRouting()->generate('widget_edit', array('id' => $widget->getId()), true);

        $ticket = TicketTable::getInstance()->create(array(
            TicketTable::CREATE_TO => $widget->getUser(),
            TicketTable::CREATE_KIND => TicketTable::KIND_WIDGET_CREATED,
            TicketTable::CREATE_WIDGET => $widget,
            TicketTable::CREATE_TEXT => 'A widget was generated using your e-mail address (' . $widget->getEmail() . '). Approve to validate, decline to block widget.'
        ));
        if ($ticket) {
          $ticket->save();
          $ticket->notifyAdmin();
        }
      }

      $from = $petition->getFrom();
      $to = $this->getObject()->getEmail();
      $additional_subst = array(
          'VALIDATION' => $validation, // deprecated
          'EDITCODE' => $edit_code, // deprecated
          '#VALIDATION-URL#' => $validation,
          '#EDIT-URL#' => $edit_code,
      );

      UtilMail::sendWithSubst(null, $from, $to, $subject, $body, $petition_text, $widget, $additional_subst);
    }
  }

  public function getStateCount() {
    return $this->state_count;
  }

}
