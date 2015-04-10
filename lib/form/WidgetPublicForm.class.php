<?php

class WidgetPublicForm extends WidgetForm {

  protected $one_side = false;

  public function isOneSide() {
    return $this->one_side;
  }

  public function configure() {
    parent::configure();
    unset($this['updated_at']);
    if (isset($this['id']))
      unset($this['id']);

    $widget = $this->getObject();
    $petition = $widget->getPetition();

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

    if (isset($this['title']))
      $this->setWidget('title', new sfWidgetFormInputHidden(array(), array('class' => 'original')));
    if (isset($this['target']))
      $this->setWidget('target', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original not_required')));
    if (isset($this['background']))
      $this->setWidget('background', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));
    if (isset($this['intro']))
      $this->setWidget('intro', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));
    if (isset($this['footer']))
      $this->setWidget('footer', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));
    if (isset($this['email_subject']))
      $this->setWidget('email_subject', new sfWidgetFormInputHidden(array(), array('class' => 'original')));
    if (isset($this['email_body']))
      $this->setWidget('email_body', new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));

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

    $this->widgetSchema->setFormFormatterName('policatWidget');
  }

  public static function utilPosition($array, $key1, $key2) {
    if (in_array($key1, $array) && in_array($key1, $array)) {
      foreach ($array as $key) {
        if ($key === $key1)
          return 2;
        if ($key === $key2)
          return true;
      }
      return true;
    }
    return false;
  }

  public function isGroupedField($name) {
    $fieldNames = array('styling_type', 'styling_width', 'styling_title_color', 'styling_body_color', 'styling_bg_left_color', 'styling_bg_right_color', 'styling_form_title_color', 'styling_button_color');
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
    }

    parent::doUpdateObject($values);
  }

  protected function doSave($con = null) {
    $wasNew = $this->getObject()->isNew();
    parent::doSave($con);

    if ($wasNew) {
      $widget = $this->getObject();
      $petition = $widget->getPetition();
      $petition_text = $widget->getPetitionText();
      $subject = 'Validate your widget';
      $body = "Validate: VALIDATION\nEdit: EDITCODE";

      $store = StoreTable::getInstance()->findByKeyAndLanguageWithFallback(StoreTable::EMBED_WIDGET_MAIL, $petition_text->getLanguageId());
      if ($store) {
        $subject = $store->getField('subject');
        $body = $store->getField('body');
      }

      $validation = UtilLink::widgetValidation($this->getObject()->getId(), $this->getObject()->getValidationData());
      $edit_code = UtilLink::widgetEdit($this->getObject()->getId(), $this->getObject()->getEditCode());
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

}
