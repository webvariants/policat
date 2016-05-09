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
      'id'              => new sfWidgetFormInputHidden(),
      'petition_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => false)),
      'fields'          => new sfWidgetFormTextarea(),
      'status'          => new sfWidgetFormInputText(),
      'verified'        => new sfWidgetFormInputText(),
      'email'           => new sfWidgetFormInputText(),
      'country'         => new sfWidgetFormInputText(),
      'validation_kind' => new sfWidgetFormInputText(),
      'validation_data' => new sfWidgetFormInputText(),
      'delete_code'     => new sfWidgetFormInputText(),
      'widget_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'add_empty' => true)),
      'wave_sent'       => new sfWidgetFormInputText(),
      'wave_pending'    => new sfWidgetFormInputText(),
      'wave_cron'       => new sfWidgetFormInputText(),
      'subscribe'       => new sfWidgetFormInputText(),
      'email_hash'      => new sfWidgetFormInputText(),
      'mailed_at'       => new sfWidgetFormDateTime(),
      'fullname'        => new sfWidgetFormInputText(),
      'title'           => new sfWidgetFormInputText(),
      'firstname'       => new sfWidgetFormInputText(),
      'lastname'        => new sfWidgetFormInputText(),
      'address'         => new sfWidgetFormInputText(),
      'city'            => new sfWidgetFormInputText(),
      'post_code'       => new sfWidgetFormInputText(),
      'comment'         => new sfWidgetFormTextarea(),
      'extra1'          => new sfWidgetFormInputText(),
      'privacy'         => new sfWidgetFormInputText(),
      'email_subject'   => new sfWidgetFormInputText(),
      'email_body'      => new sfWidgetFormTextarea(),
      'ref'             => new sfWidgetFormInputText(),
      'quota_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Quota'), 'add_empty' => true)),
      'quota_emails'    => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'contact_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Contact')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'petition_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'fields'          => new sfValidatorString(),
      'status'          => new sfValidatorInteger(array('required' => false)),
      'verified'        => new sfValidatorInteger(array('required' => false)),
      'email'           => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'country'         => new sfValidatorString(array('max_length' => 5, 'required' => false)),
      'validation_kind' => new sfValidatorInteger(array('required' => false)),
      'validation_data' => new sfValidatorString(array('max_length' => 16, 'required' => false)),
      'delete_code'     => new sfValidatorString(array('max_length' => 16, 'required' => false)),
      'widget_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'column' => 'id', 'required' => false)),
      'wave_sent'       => new sfValidatorInteger(array('required' => false)),
      'wave_pending'    => new sfValidatorInteger(array('required' => false)),
      'wave_cron'       => new sfValidatorInteger(array('required' => false)),
      'subscribe'       => new sfValidatorInteger(array('required' => false)),
      'email_hash'      => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'mailed_at'       => new sfValidatorDateTime(array('required' => false)),
      'fullname'        => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'title'           => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'firstname'       => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'lastname'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'address'         => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'city'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'post_code'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'comment'         => new sfValidatorString(array('required' => false)),
      'extra1'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'privacy'         => new sfValidatorInteger(array('required' => false)),
      'email_subject'   => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'email_body'      => new sfValidatorString(array('required' => false)),
      'ref'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'quota_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Quota'), 'column' => 'id', 'required' => false)),
      'quota_emails'    => new sfValidatorInteger(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'contact_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Contact', 'required' => false)),
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
