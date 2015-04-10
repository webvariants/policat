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
 * sfGuardUser form base class.
 *
 * @method sfGuardUser getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BasesfGuardUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'first_name'       => new sfWidgetFormInputText(),
      'last_name'        => new sfWidgetFormInputText(),
      'email_address'    => new sfWidgetFormInputText(),
      'username'         => new sfWidgetFormInputText(),
      'algorithm'        => new sfWidgetFormInputText(),
      'salt'             => new sfWidgetFormInputText(),
      'password'         => new sfWidgetFormInputText(),
      'is_active'        => new sfWidgetFormInputCheckbox(),
      'is_super_admin'   => new sfWidgetFormInputCheckbox(),
      'last_login'       => new sfWidgetFormDateTime(),
      'id'               => new sfWidgetFormInputHidden(),
      'organisation'     => new sfWidgetFormInputText(),
      'website'          => new sfWidgetFormInputText(),
      'mobile'           => new sfWidgetFormInputText(),
      'phone'            => new sfWidgetFormInputText(),
      'street'           => new sfWidgetFormInputText(),
      'city'             => new sfWidgetFormInputText(),
      'post_code'        => new sfWidgetFormInputText(),
      'country'          => new sfWidgetFormInputText(),
      'language_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => true)),
      'validation_kind'  => new sfWidgetFormInputText(),
      'validation_code'  => new sfWidgetFormInputText(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
      'groups_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardGroup')),
      'permissions_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardPermission')),
      'campaign_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Campaign')),
    ));

    $this->setValidators(array(
      'first_name'       => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'last_name'        => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'email_address'    => new sfValidatorString(array('max_length' => 80)),
      'username'         => new sfValidatorString(array('max_length' => 128)),
      'algorithm'        => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'salt'             => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'password'         => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'is_active'        => new sfValidatorBoolean(array('required' => false)),
      'is_super_admin'   => new sfValidatorBoolean(array('required' => false)),
      'last_login'       => new sfValidatorDateTime(array('required' => false)),
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'organisation'     => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'website'          => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'mobile'           => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'phone'            => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'street'           => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'city'             => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'post_code'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'country'          => new sfValidatorString(array('max_length' => 2, 'required' => false)),
      'language_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id', 'required' => false)),
      'validation_kind'  => new sfValidatorInteger(array('required' => false)),
      'validation_code'  => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
      'groups_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardGroup', 'required' => false)),
      'permissions_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardPermission', 'required' => false)),
      'campaign_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Campaign', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('email_address'))),
        new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('username'))),
      ))
    );

    $this->widgetSchema->setNameFormat('sf_guard_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfGuardUser';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['groups_list']))
    {
      $this->setDefault('groups_list', $this->object->Groups->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['permissions_list']))
    {
      $this->setDefault('permissions_list', $this->object->Permissions->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['campaign_list']))
    {
      $this->setDefault('campaign_list', $this->object->Campaign->getPrimaryKeys());
    }

  }

  protected function doUpdateObject($values)
  {
    $this->updateGroupsList($values);
    $this->updatePermissionsList($values);
    $this->updateCampaignList($values);

    parent::doUpdateObject($values);
  }

  public function updateGroupsList($values)
  {
    if (!isset($this->widgetSchema['groups_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('groups_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->Groups->getPrimaryKeys();
    $values = $values['groups_list'];
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Groups', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Groups', array_values($link));
    }
  }

  public function updatePermissionsList($values)
  {
    if (!isset($this->widgetSchema['permissions_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('permissions_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->Permissions->getPrimaryKeys();
    $values = $values['permissions_list'];
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Permissions', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Permissions', array_values($link));
    }
  }

  public function updateCampaignList($values)
  {
    if (!isset($this->widgetSchema['campaign_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('campaign_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->Campaign->getPrimaryKeys();
    $values = $values['campaign_list'];
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Campaign', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Campaign', array_values($link));
    }
  }

}
