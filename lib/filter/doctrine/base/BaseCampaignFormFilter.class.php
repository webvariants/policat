<?php

/**
 * Campaign filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseCampaignFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'status'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'billing_enabled'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'owner_register'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'allow_download'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'become_petition_admin' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'privacy_policy'        => new sfWidgetFormFilterInput(),
      'address'               => new sfWidgetFormFilterInput(),
      'data_owner_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DataOwner'), 'add_empty' => true)),
      'quota_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Quota'), 'add_empty' => true)),
      'order_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Order'), 'add_empty' => true)),
      'public_enabled'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'join_enabled'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'object_version'        => new sfWidgetFormFilterInput(),
      'sf_guard_user_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardUser')),
    ));

    $this->setValidators(array(
      'name'                  => new sfValidatorPass(array('required' => false)),
      'status'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'billing_enabled'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'owner_register'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'allow_download'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'become_petition_admin' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'privacy_policy'        => new sfValidatorPass(array('required' => false)),
      'address'               => new sfValidatorPass(array('required' => false)),
      'data_owner_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DataOwner'), 'column' => 'id')),
      'quota_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Quota'), 'column' => 'id')),
      'order_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Order'), 'column' => 'id')),
      'public_enabled'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'join_enabled'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'object_version'        => new sfValidatorPass(array('required' => false)),
      'sf_guard_user_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardUser', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('campaign_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addSfGuardUserListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.Member Member')
      ->andWhereIn('Member.sf_guard_user_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Campaign';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'name'                  => 'Text',
      'status'                => 'Number',
      'billing_enabled'       => 'Number',
      'owner_register'        => 'Number',
      'allow_download'        => 'Number',
      'become_petition_admin' => 'Number',
      'privacy_policy'        => 'Text',
      'address'               => 'Text',
      'data_owner_id'         => 'ForeignKey',
      'quota_id'              => 'ForeignKey',
      'order_id'              => 'ForeignKey',
      'public_enabled'        => 'Number',
      'join_enabled'          => 'Number',
      'created_at'            => 'Date',
      'updated_at'            => 'Date',
      'object_version'        => 'Text',
      'sf_guard_user_list'    => 'ManyKey',
    );
  }
}
