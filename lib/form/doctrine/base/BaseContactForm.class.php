<?php

/**
 * Contact form base class.
 *
 * @method Contact getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseContactForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'status'                => new sfWidgetFormInputText(),
      'mailing_list_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MailingList'), 'add_empty' => false)),
      'email'                 => new sfWidgetFormInputText(),
      'gender'                => new sfWidgetFormInputText(),
      'firstname'             => new sfWidgetFormInputText(),
      'lastname'              => new sfWidgetFormInputText(),
      'country'               => new sfWidgetFormInputText(),
      'language_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => true)),
      'bounce'                => new sfWidgetFormInputText(),
      'bounce_at'             => new sfWidgetFormDateTime(),
      'bounce_blocked'        => new sfWidgetFormInputText(),
      'bounce_hard'           => new sfWidgetFormInputText(),
      'bounce_related_to'     => new sfWidgetFormInputText(),
      'bounce_error'          => new sfWidgetFormInputText(),
      'petition_signing_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'PetitionSigning')),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'status'                => new sfValidatorInteger(array('required' => false)),
      'mailing_list_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('MailingList'), 'column' => 'id')),
      'email'                 => new sfValidatorString(array('max_length' => 80)),
      'gender'                => new sfValidatorInteger(),
      'firstname'             => new sfValidatorString(array('max_length' => 100)),
      'lastname'              => new sfValidatorString(array('max_length' => 100)),
      'country'               => new sfValidatorString(array('max_length' => 5, 'required' => false)),
      'language_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id', 'required' => false)),
      'bounce'                => new sfValidatorInteger(array('required' => false)),
      'bounce_at'             => new sfValidatorDateTime(array('required' => false)),
      'bounce_blocked'        => new sfValidatorInteger(array('required' => false)),
      'bounce_hard'           => new sfValidatorInteger(array('required' => false)),
      'bounce_related_to'     => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'bounce_error'          => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'petition_signing_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'PetitionSigning', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('contact[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Contact';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['petition_signing_list']))
    {
      $this->setDefault('petition_signing_list', $this->object->PetitionSigning->getPrimaryKeys());
    }

  }

  protected function doUpdateObject($values)
  {
    $this->updatePetitionSigningList($values);

    parent::doUpdateObject($values);
  }

  public function updatePetitionSigningList($values)
  {
    if (!isset($this->widgetSchema['petition_signing_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('petition_signing_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->PetitionSigning->getPrimaryKeys();
    $values = $values['petition_signing_list'];
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('PetitionSigning', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('PetitionSigning', array_values($link));
    }
  }

}
