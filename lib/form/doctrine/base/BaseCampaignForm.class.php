<?php

/**
 * Campaign form base class.
 *
 * @method Campaign getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseCampaignForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'name'                  => new sfWidgetFormTextarea(),
      'status'                => new sfWidgetFormInputText(),
      'owner_register'        => new sfWidgetFormInputText(),
      'allow_download'        => new sfWidgetFormInputText(),
      'become_petition_admin' => new sfWidgetFormInputText(),
      'privacy_policy'        => new sfWidgetFormTextarea(),
      'address'               => new sfWidgetFormTextarea(),
      'data_owner_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DataOwner'), 'add_empty' => true)),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'object_version'        => new sfWidgetFormInputText(),
      'sf_guard_user_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardUser')),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                  => new sfValidatorString(),
      'status'                => new sfValidatorInteger(array('required' => false)),
      'owner_register'        => new sfValidatorInteger(array('required' => false)),
      'allow_download'        => new sfValidatorInteger(array('required' => false)),
      'become_petition_admin' => new sfValidatorInteger(array('required' => false)),
      'privacy_policy'        => new sfValidatorString(array('required' => false)),
      'address'               => new sfValidatorString(array('required' => false)),
      'data_owner_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DataOwner'), 'column' => 'id', 'required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
      'object_version'        => new sfValidatorString(array('max_length' => 15, 'required' => false)),
      'sf_guard_user_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardUser', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('campaign[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Campaign';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['sf_guard_user_list']))
    {
      $this->setDefault('sf_guard_user_list', $this->object->sfGuardUser->getPrimaryKeys());
    }

  }

  protected function doUpdateObject($values)
  {
    $this->updatesfGuardUserList($values);

    parent::doUpdateObject($values);
  }

  public function updatesfGuardUserList($values)
  {
    if (!isset($this->widgetSchema['sf_guard_user_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('sf_guard_user_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->sfGuardUser->getPrimaryKeys();
    $values = $values['sf_guard_user_list'];
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('sfGuardUser', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('sfGuardUser', array_values($link));
    }
  }

}
