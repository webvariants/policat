<?php

/**
 * PetitionText form base class.
 *
 * @method PetitionText getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BasePetitionTextForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                           => new sfWidgetFormInputHidden(),
      'status'                       => new sfWidgetFormInputText(),
      'language_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => false)),
      'petition_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => false)),
      'title'                        => new sfWidgetFormTextarea(),
      'target'                       => new sfWidgetFormTextarea(),
      'background'                   => new sfWidgetFormTextarea(),
      'intro'                        => new sfWidgetFormTextarea(),
      'body'                         => new sfWidgetFormTextarea(),
      'footer'                       => new sfWidgetFormTextarea(),
      'email_subject'                => new sfWidgetFormTextarea(),
      'email_body'                   => new sfWidgetFormTextarea(),
      'thank_you_email_subject'      => new sfWidgetFormTextarea(),
      'thank_you_email_body'         => new sfWidgetFormTextarea(),
      'email_validation_subject'     => new sfWidgetFormTextarea(),
      'email_validation_body'        => new sfWidgetFormTextarea(),
      'email_tellyour_subject'       => new sfWidgetFormTextarea(),
      'email_tellyour_body'          => new sfWidgetFormTextarea(),
      'email_targets'                => new sfWidgetFormTextarea(),
      'privacy_policy_body'          => new sfWidgetFormTextarea(),
      'landing_url'                  => new sfWidgetFormTextarea(),
      'widget_id'                    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DefaultWidget'), 'add_empty' => true)),
      'pledge_title'                 => new sfWidgetFormTextarea(),
      'pledge_comment'               => new sfWidgetFormTextarea(),
      'pledge_explantory_annotation' => new sfWidgetFormTextarea(),
      'pledge_thank_you'             => new sfWidgetFormTextarea(),
      'donate_url'                   => new sfWidgetFormInputText(),
      'donate_text'                  => new sfWidgetFormTextarea(),
      'signers_url'                  => new sfWidgetFormInputText(),
      'label_extra1'                 => new sfWidgetFormInputText(),
      'placeholder_extra1'           => new sfWidgetFormInputText(),
      'form_title'                   => new sfWidgetFormInputText(),
      'subscribe_text'               => new sfWidgetFormInputText(),
      'signers_page'                 => new sfWidgetFormTextarea(),
      'created_at'                   => new sfWidgetFormDateTime(),
      'updated_at'                   => new sfWidgetFormDateTime(),
      'object_version'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'status'                       => new sfValidatorInteger(array('required' => false)),
      'language_id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id')),
      'petition_id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'title'                        => new sfValidatorString(array('required' => false)),
      'target'                       => new sfValidatorString(array('required' => false)),
      'background'                   => new sfValidatorString(array('required' => false)),
      'intro'                        => new sfValidatorString(array('required' => false)),
      'body'                         => new sfValidatorString(array('required' => false)),
      'footer'                       => new sfValidatorString(array('required' => false)),
      'email_subject'                => new sfValidatorString(array('required' => false)),
      'email_body'                   => new sfValidatorString(array('required' => false)),
      'thank_you_email_subject'      => new sfValidatorString(array('required' => false)),
      'thank_you_email_body'         => new sfValidatorString(array('required' => false)),
      'email_validation_subject'     => new sfValidatorString(),
      'email_validation_body'        => new sfValidatorString(array('required' => false)),
      'email_tellyour_subject'       => new sfValidatorString(),
      'email_tellyour_body'          => new sfValidatorString(array('required' => false)),
      'email_targets'                => new sfValidatorString(array('required' => false)),
      'privacy_policy_body'          => new sfValidatorString(array('required' => false)),
      'landing_url'                  => new sfValidatorString(array('required' => false)),
      'widget_id'                    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DefaultWidget'), 'column' => 'id', 'required' => false)),
      'pledge_title'                 => new sfValidatorString(array('required' => false)),
      'pledge_comment'               => new sfValidatorString(array('required' => false)),
      'pledge_explantory_annotation' => new sfValidatorString(array('required' => false)),
      'pledge_thank_you'             => new sfValidatorString(array('required' => false)),
      'donate_url'                   => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'donate_text'                  => new sfValidatorString(array('required' => false)),
      'signers_url'                  => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'label_extra1'                 => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'placeholder_extra1'           => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'form_title'                   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'subscribe_text'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'signers_page'                 => new sfValidatorString(array('required' => false)),
      'created_at'                   => new sfValidatorDateTime(),
      'updated_at'                   => new sfValidatorDateTime(),
      'object_version'               => new sfValidatorString(array('max_length' => 15, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('petition_text[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PetitionText';
  }

}
