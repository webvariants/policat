<?php

/**
 * PetitionSigning form base class.
 *
 * @method PetitionSigning getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BasePetitionSigningForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'petition_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => false)),
      'campaign_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => true)),
      'petition_text_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PetitionText'), 'add_empty' => true)),
      'language_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => true)),
      'fields'                 => new sfWidgetFormTextarea(),
      'status'                 => new sfWidgetFormInputText(),
      'petition_status'        => new sfWidgetFormInputText(),
      'petition_enabled'       => new sfWidgetFormInputText(),
      'verified'               => new sfWidgetFormInputText(),
      'email'                  => new sfWidgetFormInputText(),
      'country'                => new sfWidgetFormInputText(),
      'validation_kind'        => new sfWidgetFormInputText(),
      'validation_data'        => new sfWidgetFormInputText(),
      'delete_code'            => new sfWidgetFormInputText(),
      'widget_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'add_empty' => true)),
      'wave_sent'              => new sfWidgetFormInputText(),
      'wave_pending'           => new sfWidgetFormInputText(),
      'wave_cron'              => new sfWidgetFormInputText(),
      'subscribe'              => new sfWidgetFormInputText(),
      'email_hash'             => new sfWidgetFormInputText(),
      'mailed_at'              => new sfWidgetFormDateTime(),
      'fullname'               => new sfWidgetFormInputText(),
      'title'                  => new sfWidgetFormInputText(),
      'firstname'              => new sfWidgetFormInputText(),
      'lastname'               => new sfWidgetFormInputText(),
      'address'                => new sfWidgetFormInputText(),
      'city'                   => new sfWidgetFormInputText(),
      'post_code'              => new sfWidgetFormInputText(),
      'comment'                => new sfWidgetFormTextarea(),
      'extra1'                 => new sfWidgetFormInputText(),
      'extra2'                 => new sfWidgetFormInputText(),
      'extra3'                 => new sfWidgetFormInputText(),
      'privacy'                => new sfWidgetFormInputText(),
      'email_subject'          => new sfWidgetFormInputText(),
      'email_body'             => new sfWidgetFormTextarea(),
      'ref'                    => new sfWidgetFormInputText(),
      'quota_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Quota'), 'add_empty' => true)),
      'quota_emails'           => new sfWidgetFormInputText(),
      'thank_sent'             => new sfWidgetFormInputText(),
      'quota_thank_you_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('QuotaThankYou'), 'add_empty' => true)),
      'bounce'                 => new sfWidgetFormInputText(),
      'bounce_at'              => new sfWidgetFormDateTime(),
      'bounce_blocked'         => new sfWidgetFormInputText(),
      'bounce_hard'            => new sfWidgetFormInputText(),
      'bounce_related_to'      => new sfWidgetFormInputText(),
      'bounce_error'           => new sfWidgetFormInputText(),
      'download_subscriber_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DownloadSubscriber'), 'add_empty' => true)),
      'download_data_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DownloadData'), 'add_empty' => true)),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
      'contact_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Contact')),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'petition_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'campaign_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id', 'required' => false)),
      'petition_text_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PetitionText'), 'column' => 'id', 'required' => false)),
      'language_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id', 'required' => false)),
      'fields'                 => new sfValidatorString(array('required' => false)),
      'status'                 => new sfValidatorInteger(array('required' => false)),
      'petition_status'        => new sfValidatorInteger(array('required' => false)),
      'petition_enabled'       => new sfValidatorInteger(array('required' => false)),
      'verified'               => new sfValidatorInteger(array('required' => false)),
      'email'                  => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'country'                => new sfValidatorString(array('max_length' => 5, 'required' => false)),
      'validation_kind'        => new sfValidatorInteger(array('required' => false)),
      'validation_data'        => new sfValidatorString(array('max_length' => 16, 'required' => false)),
      'delete_code'            => new sfValidatorString(array('max_length' => 16, 'required' => false)),
      'widget_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'column' => 'id', 'required' => false)),
      'wave_sent'              => new sfValidatorInteger(array('required' => false)),
      'wave_pending'           => new sfValidatorInteger(array('required' => false)),
      'wave_cron'              => new sfValidatorInteger(array('required' => false)),
      'subscribe'              => new sfValidatorInteger(array('required' => false)),
      'email_hash'             => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'mailed_at'              => new sfValidatorDateTime(array('required' => false)),
      'fullname'               => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'title'                  => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'firstname'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'lastname'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'address'                => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'city'                   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'post_code'              => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'comment'                => new sfValidatorString(array('required' => false)),
      'extra1'                 => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'extra2'                 => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'extra3'                 => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'privacy'                => new sfValidatorInteger(array('required' => false)),
      'email_subject'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'email_body'             => new sfValidatorString(array('required' => false)),
      'ref'                    => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'quota_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Quota'), 'column' => 'id', 'required' => false)),
      'quota_emails'           => new sfValidatorInteger(array('required' => false)),
      'thank_sent'             => new sfValidatorInteger(array('required' => false)),
      'quota_thank_you_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('QuotaThankYou'), 'column' => 'id', 'required' => false)),
      'bounce'                 => new sfValidatorInteger(array('required' => false)),
      'bounce_at'              => new sfValidatorDateTime(array('required' => false)),
      'bounce_blocked'         => new sfValidatorInteger(array('required' => false)),
      'bounce_hard'            => new sfValidatorInteger(array('required' => false)),
      'bounce_related_to'      => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'bounce_error'           => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'download_subscriber_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DownloadSubscriber'), 'column' => 'id', 'required' => false)),
      'download_data_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DownloadData'), 'column' => 'id', 'required' => false)),
      'created_at'             => new sfValidatorDateTime(),
      'updated_at'             => new sfValidatorDateTime(),
      'contact_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Contact', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('petition_signing[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PetitionSigning';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['contact_list']))
    {
      $this->setDefault('contact_list', $this->object->Contact->getPrimaryKeys());
    }

  }

  protected function doUpdateObject($values)
  {
    $this->updateContactList($values);

    parent::doUpdateObject($values);
  }

  public function updateContactList($values)
  {
    if (!isset($this->widgetSchema['contact_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('contact_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->Contact->getPrimaryKeys();
    $values = $values['contact_list'];
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Contact', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Contact', array_values($link));
    }
  }

}
