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
 * PetitionSigning form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PetitionSigningForm extends BasePetitionSigningForm {

  protected $no_mails = false;
  private $contact_num = 0;

  public function getNoMails() {
    return $this->no_mails;
  }

  public function configure() {
    $this->useFields(array('id', 'email', 'subscribe'));
    $this->disableLocalCSRFProtection();
    $widget_object = $this->getObject()->getWidget();
    $petition = $this->getObject()->getPetition();

    $this->formfields = $this->getObject()->getPetition()->getFormfields();

    $this->setValidator(Petition::FIELD_REF, new sfValidatorString(array('required' => false)));

    foreach ($this->formfields as $formfield) {
      if (isset($this[$formfield])) {
        unset($this[$formfield]);
      }
      $widget = null;
      $validator = null;
      $label = true;
      switch ($formfield) {
        case Petition::FIELD_EMAIL:
          $widget = new sfWidgetFormInputText();
          $validator = new ValidatorUniqueEmail(array(
              'petition_id' => $petition['id'],
              'max_length' => 80,
              ValidatorUniqueEmail::OPTION_IS_GEO => $petition->isGeoKind(),
              ValidatorUniqueEmail::OPTION_IGNORE_PENDING => true
          ));
          $label = 'Email address';
          break;
        case Petition::FIELD_COUNTRY:
          $culture_info = $widget_object->getPetitionText()->utilCultureInfo();
          if ($petition->getCountryCollectionId()) {
            $countries = $petition->getCountryCollection()->getCountriesList();
          } else {
            $countries = $widget_object->getPetitionText()->utilCountries();
          }

          $widget = new sfWidgetFormI18nChoiceCountry(array('countries' => $countries, 'culture' => $culture_info->getName(), 'add_empty' => 'Country'));
          $validator = new sfValidatorI18nChoiceCountry(array('countries' => $countries));
          $label = false;
          break;
        case Petition::FIELD_PRIVACY:
          $widget = new WidgetFormInputCheckbox(array('value_attribute_value' => 1));
          $validator = new sfValidatorChoice(array('choices' => array('1'), 'required' => true));
          $label = 'Privacy policy';
          break;
        case Petition::FIELD_SUBSCRIBE:
          $widget = new WidgetFormInputCheckbox(array('value_attribute_value' => 1), array('checked' => 'checked'));
          $validator = new sfValidatorChoice(array('choices' => array('1'), 'required' => false));
          $label = 'Keep me posted on this and similar campaigns.';
          break;
        case Petition::FIELD_TITLE:
          $widget = new sfWidgetFormChoice(array('choices' => array('' => '', 'female' => 'Mrs', 'male' => 'Mr', 'nogender' => 'Hello')));
          $validator = new sfValidatorChoice(array('choices' => array('male', 'female', 'nogender')));
          $label = 'Mrs/Mr';
          break;
        case Petition::FIELD_COMMENT:
          $widget = new sfWidgetFormTextarea();
          $validator = new sfValidatorString(array('required' => false));
          break;
        case Petition::FIELD_FIRSTNAME:
          $widget = new sfWidgetFormInputText();
          $validator = new sfValidatorString();
          $label = 'First name';
          break;
        case Petition::FIELD_LASTNAME:
          $widget = new sfWidgetFormInputText();
          $validator = new sfValidatorString();
          $label = 'Last name';
          break;
        case Petition::FIELD_FULLNAME:
          $widget = new sfWidgetFormInputText();
          $validator = new sfValidatorString();
          $label = 'Full name';
          break;
        default:
          $widget = new sfWidgetFormInputText();
          $validator = new sfValidatorString();
      }
      if (isset($widget)) {
        $this->setWidget($formfield, $widget);
      }
      if (isset($widget)) {
        $this->setValidator($formfield, $validator);
      }
      if ($label !== true) {
        $this->getWidgetSchema()->setLabel($formfield, $label);
      }
    }

    if ($petition->isEmailKind()) {
      if ($petition->getKind() != Petition::KIND_PLEDGE) {
        $petition_text = $widget_object->getPetitionText();
        $this->setWidget(Petition::FIELD_EMAIL_SUBJECT, new sfWidgetFormInputHidden(array(), array('class' => 'original')));
        $this->setValidator(Petition::FIELD_EMAIL_SUBJECT, new sfValidatorString(array('required' => true)));
        $widget_texts = $petition->getWidgetIndividualiseText();
        $w_subject = $widget_object->getEmailSubject();
        $this->setDefault(Petition::FIELD_EMAIL_SUBJECT, ($widget_texts && trim($w_subject)) ? $w_subject : $petition_text->getEmailSubject());
        $this->setWidget(Petition::FIELD_EMAIL_BODY, new sfWidgetFormTextarea(array('is_hidden' => true), array('class' => 'original')));
        $this->setValidator(Petition::FIELD_EMAIL_BODY, new sfValidatorString(array('required' => true)));
        $w_body = $widget_object->getEmailBody();
        $this->setDefault(Petition::FIELD_EMAIL_BODY, ($widget_texts && trim($w_body)) ? $w_body : $petition_text->getEmailBody());
      } else {
        $this->setWidget('pledges', new sfWidgetFormInputHidden());
        $this->setValidator('pledges', new sfValidatorString(array('required' => false)));
      }

      if ($petition->isGeoKind() && $petition->getKind() != Petition::KIND_PLEDGE) {
        $this->setWidget('ts_1', new sfWidgetFormInputHidden(array(), array('class' => 'original')));
        $this->setValidator('ts_1', new sfValidatorString(array('required' => false)));
        $this->setWidget('ts_2', new sfWidgetFormInputHidden(array(), array('class' => 'original')));
        $this->setValidator('ts_2', new sfValidatorString(array('required' => false)));
      }
    }

    $this->widgetSchema->setFormFormatterName('policatWidget');
  }

  public function selectFormatter($widget, $inputs) {
    $rows = array();
    foreach ($inputs as $input) {
      $rows[] = sprintf('<div class="input_checkbox">%s</div>%s%s', $input['input'], $widget->getOption('label_separator'), $input['label']);
    }

    return join('', $rows);
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
    if (in_array($name, $this->fieldNames)) {
      switch ($name) {
        case Petition::FIELD_CITY: return self::utilPosition($this->fieldNames, Petition::FIELD_CITY, Petition::FIELD_POSTCODE);
        case Petition::FIELD_POSTCODE: return self::utilPosition($this->fieldNames, Petition::FIELD_POSTCODE, Petition::FIELD_CITY);
        case Petition::FIELD_TITLE: return self::utilPosition($this->fieldNames, Petition::FIELD_TITLE, Petition::FIELD_FIRSTNAME);
        case Petition::FIELD_FIRSTNAME: return self::utilPosition($this->fieldNames, Petition::FIELD_FIRSTNAME, Petition::FIELD_TITLE);
      }
    }
    return false;
  }

  protected function doUpdateObject($values) {
    $code = PetitionSigning::genCode();

    if ($this->getObject()->getPetition()->isGeoKind()) {
        // EMAIL-TO-LIST ACTION (AND PLEDGE)
      $fields = array();
      $formFields = array();
      foreach ($this->formfields as $fieldname) {
        $formFields[] = $fieldname;
      }
      if ($this->getObject()->getPetition()->isEmailKind()) {
        $formFields[] = Petition::FIELD_EMAIL_SUBJECT;
        $formFields[] = Petition::FIELD_EMAIL_BODY;
      }
      $non_json_fields = array('email', 'country', 'subscribe');
      foreach ($formFields as $fieldname) {
        if (!in_array($fieldname, $non_json_fields)) {
          if (array_key_exists($fieldname, $values)) {
            $fields[$fieldname] = $values[$fieldname];
          }
        }
      }

      $fields[Petition::FIELD_REF] = $values[Petition::FIELD_REF];

      $wave = new PetitionSigningWave();
      $wave->setWave($this->getObject()->getWavePending());
      $wave->setFields(json_encode($fields));
      $wave->setEmail($this->getValue(Petition::FIELD_EMAIL));
      $wave->setCountry($this->getObject()->getPetition()->getWithCountry() ? $this->getValue(Petition::FIELD_COUNTRY) : $this->getObject()->getPetition()->getDefaultCountry());
      $wave->setValidationData($code);
      $wave->setLanguageId($this->getObject()->getWidget()->getPetitionText()->getLanguageId());
      $wave->setWidgetId($this->getObject()->getWidgetId());
      $wave->setContactNum($this->contact_num);
      $object = $this->getObject();
      $object['PetitionSigningWave'][] = $wave;

    }

    if (!$this->getObject()->isNew()) {
      unset($values[Petition::FIELD_EMAIL_SUBJECT], $values[Petition::FIELD_EMAIL_BODY]);
    }

    if (!$this->getObject()->getPetition()->getWithCountry()) {
      $values['country'] = $this->getObject()->getPetition()->getDefaultCountry();
    }

    $validation_kind = $this->getOption('validation_kind', PetitionSigning::VALIDATION_KIND_NONE);
    switch ($validation_kind) {
      case PetitionSigning::VALIDATION_KIND_EMAIL:
        $values['validation_data'] = $code;
        $values['validation_kind'] = PetitionSigning::VALIDATION_KIND_EMAIL;
        break;
      case PetitionSigning::VALIDATION_KIND_NONE:
      default:
        $values['validation_kind'] = PetitionSigning::VALIDATION_KIND_NONE;
        break;
    }

    unset($values['id']);
    parent::doUpdateObject($values);
  }

  protected function doSave($con = null) {
    if (null === $con) {
      $con = $this->getConnection();
    }

    $signing = $this->getObject();
    $petition = $signing->getPetition();
    $geo_existing = false;
    if ($petition->isGeoKind()) {
      // EMAIL-TO-LIST ACTION (AND PLEDGE)
      $existing_signing = PetitionSigningTable::getInstance()->findByPetitionIdAndEmail($petition->getId(), $this->getValue('email'));
      if ($existing_signing) {
        $geo_existing = true;
        $existing_signing->setPetition($petition);
        $this->object = $existing_signing;
        $signing = $existing_signing;
        $this->isNew = false;
        $signing->setWavePending($signing->getWavePending() + 1);
      } else {
        $signing->setWavePending(1);
      }

      $this->contact_num = 0;

      if ($petition->getKind() == Petition::KIND_PLEDGE) {
        $targets = ContactTable::getInstance()->fetchIdsByContactIds($petition, $this->getValue('pledges'), $existing_signing);
      } else {
        $targets = ContactTable::getInstance()->fetchIdsByTargetSelector($petition, $this->getValue('ts_1'), $this->getValue('ts_2'), $existing_signing);
      }

      if ($targets) {
        foreach (((array) $targets) as $target) {
          $signing_contact = new PetitionSigningContact();
          $signing['PetitionSigningContact'][] = $signing_contact;
          $signing_contact->setContactId($target['id']);
          $signing_contact->setWave($signing->getWavePending());
          $this->contact_num++;
        }
        parent::doSave($con);
      } else {
        $this->no_mails = true;
      }
    } else {
      parent::doSave($con);
    }

    $existing_signing = PetitionSigningTable::getInstance()->findByPetitionIdAndEmail($petition->getId(), $signing->getEmail(), $signing->getId());
    if ($existing_signing) {
      if ($existing_signing->getStatus() == PetitionSigning::STATUS_PENDING && !$geo_existing) {
        $existing_signing->delete();
      } else {
        $signing->delete();
        $this->object = $existing_signing;
        $signing = $existing_signing;
        return;
      }
    }

    $validation_kind = $this->getOption('validation_kind', PetitionSigning::VALIDATION_KIND_NONE);
    switch ($validation_kind) {
      case PetitionSigning::VALIDATION_KIND_EMAIL:
        UtilEmailValidation::send($signing);
        break;
      case PetitionSigning::VALIDATION_KIND_NONE:
      default:
        break;
    }
  }

}
