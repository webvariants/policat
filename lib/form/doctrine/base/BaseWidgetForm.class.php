<?php

/**
 * Widget form base class.
 *
 * @method Widget getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseWidgetForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'parent_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'campaign_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => false)),
      'petition_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => false)),
      'petition_text_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PetitionText'), 'add_empty' => false)),
      'origin_widget_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('OriginWidget'), 'add_empty' => true)),
      'status'              => new sfWidgetFormInputText(),
      'title'               => new sfWidgetFormTextarea(),
      'target'              => new sfWidgetFormTextarea(),
      'background'          => new sfWidgetFormTextarea(),
      'intro'               => new sfWidgetFormTextarea(),
      'footer'              => new sfWidgetFormTextarea(),
      'email_subject'       => new sfWidgetFormTextarea(),
      'email_body'          => new sfWidgetFormTextarea(),
      'stylings'            => new sfWidgetFormTextarea(),
      'themeId'             => new sfWidgetFormInputText(),
      'email'               => new sfWidgetFormInputText(),
      'organisation'        => new sfWidgetFormInputText(),
      'landing_url'         => new sfWidgetFormTextarea(),
      'landing2_url'        => new sfWidgetFormTextarea(),
      'donate_url'          => new sfWidgetFormInputText(),
      'donate_text'         => new sfWidgetFormTextarea(),
      'ref'                 => new sfWidgetFormTextarea(),
      'validation_kind'     => new sfWidgetFormInputText(),
      'validation_data'     => new sfWidgetFormTextarea(),
      'validation_status'   => new sfWidgetFormInputText(),
      'edit_code'           => new sfWidgetFormInputText(),
      'paypal_email'        => new sfWidgetFormInputText(),
      'share'               => new sfWidgetFormInputText(),
      'user_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'data_owner'          => new sfWidgetFormInputText(),
      'activity_at'         => new sfWidgetFormDateTime(),
      'last_ref'            => new sfWidgetFormInputText(),
      'email_targets'       => new sfWidgetFormTextarea(),
      'social_share_text'   => new sfWidgetFormTextarea(),
      'cron_signings24'     => new sfWidgetFormInputText(),
      'default_country'     => new sfWidgetFormInputText(),
      'subscribe_default'   => new sfWidgetFormInputText(),
      'subscribe_text'      => new sfWidgetFormInputText(),
      'privacy_policy_body' => new sfWidgetFormTextarea(),
      'privacy_policy_url'  => new sfWidgetFormTextarea(),
      'read_more_url'       => new sfWidgetFormTextarea(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
      'object_version'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'parent_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id', 'required' => false)),
      'campaign_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id')),
      'petition_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'petition_text_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PetitionText'), 'column' => 'id')),
      'origin_widget_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('OriginWidget'), 'column' => 'id', 'required' => false)),
      'status'              => new sfValidatorInteger(array('required' => false)),
      'title'               => new sfValidatorString(array('required' => false)),
      'target'              => new sfValidatorString(array('required' => false)),
      'background'          => new sfValidatorString(array('required' => false)),
      'intro'               => new sfValidatorString(array('required' => false)),
      'footer'              => new sfValidatorString(array('required' => false)),
      'email_subject'       => new sfValidatorString(array('required' => false)),
      'email_body'          => new sfValidatorString(array('required' => false)),
      'stylings'            => new sfValidatorString(),
      'themeId'             => new sfValidatorInteger(array('required' => false)),
      'email'               => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'organisation'        => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'landing_url'         => new sfValidatorString(array('required' => false)),
      'landing2_url'        => new sfValidatorString(array('required' => false)),
      'donate_url'          => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'donate_text'         => new sfValidatorString(array('required' => false)),
      'ref'                 => new sfValidatorString(array('required' => false)),
      'validation_kind'     => new sfValidatorInteger(array('required' => false)),
      'validation_data'     => new sfValidatorString(array('required' => false)),
      'validation_status'   => new sfValidatorInteger(array('required' => false)),
      'edit_code'           => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'paypal_email'        => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'share'               => new sfValidatorInteger(array('required' => false)),
      'user_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id', 'required' => false)),
      'data_owner'          => new sfValidatorInteger(array('required' => false)),
      'activity_at'         => new sfValidatorDateTime(array('required' => false)),
      'last_ref'            => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'email_targets'       => new sfValidatorString(array('required' => false)),
      'social_share_text'   => new sfValidatorString(array('required' => false)),
      'cron_signings24'     => new sfValidatorInteger(array('required' => false)),
      'default_country'     => new sfValidatorString(array('max_length' => 5, 'required' => false)),
      'subscribe_default'   => new sfValidatorInteger(array('required' => false)),
      'subscribe_text'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'privacy_policy_body' => new sfValidatorString(array('required' => false)),
      'privacy_policy_url'  => new sfValidatorString(array('required' => false)),
      'read_more_url'       => new sfValidatorString(array('required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
      'object_version'      => new sfValidatorString(array('max_length' => 15, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('widget[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Widget';
  }

}
