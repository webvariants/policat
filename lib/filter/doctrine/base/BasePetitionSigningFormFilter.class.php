<?php

/**
 * PetitionSigning filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BasePetitionSigningFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'petition_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => true)),
      'fields'             => new sfWidgetFormFilterInput(),
      'status'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'verified'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'email'              => new sfWidgetFormFilterInput(),
      'country'            => new sfWidgetFormFilterInput(),
      'validation_kind'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'validation_data'    => new sfWidgetFormFilterInput(),
      'delete_code'        => new sfWidgetFormFilterInput(),
      'widget_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'add_empty' => true)),
      'wave_sent'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'wave_pending'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'wave_cron'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'subscribe'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'email_hash'         => new sfWidgetFormFilterInput(),
      'mailed_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'fullname'           => new sfWidgetFormFilterInput(),
      'title'              => new sfWidgetFormFilterInput(),
      'firstname'          => new sfWidgetFormFilterInput(),
      'lastname'           => new sfWidgetFormFilterInput(),
      'address'            => new sfWidgetFormFilterInput(),
      'city'               => new sfWidgetFormFilterInput(),
      'post_code'          => new sfWidgetFormFilterInput(),
      'comment'            => new sfWidgetFormFilterInput(),
      'extra1'             => new sfWidgetFormFilterInput(),
      'privacy'            => new sfWidgetFormFilterInput(),
      'email_subject'      => new sfWidgetFormFilterInput(),
      'email_body'         => new sfWidgetFormFilterInput(),
      'ref'                => new sfWidgetFormFilterInput(),
      'quota_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Quota'), 'add_empty' => true)),
      'quota_emails'       => new sfWidgetFormFilterInput(),
      'thank_sent'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'quota_thank_you_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('QuotaThankYou'), 'add_empty' => true)),
      'created_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'contact_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Contact')),
    ));

    $this->setValidators(array(
      'petition_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'fields'             => new sfValidatorPass(array('required' => false)),
      'status'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'verified'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'email'              => new sfValidatorPass(array('required' => false)),
      'country'            => new sfValidatorPass(array('required' => false)),
      'validation_kind'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'validation_data'    => new sfValidatorPass(array('required' => false)),
      'delete_code'        => new sfValidatorPass(array('required' => false)),
      'widget_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Widget'), 'column' => 'id')),
      'wave_sent'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'wave_pending'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'wave_cron'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'subscribe'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'email_hash'         => new sfValidatorPass(array('required' => false)),
      'mailed_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'fullname'           => new sfValidatorPass(array('required' => false)),
      'title'              => new sfValidatorPass(array('required' => false)),
      'firstname'          => new sfValidatorPass(array('required' => false)),
      'lastname'           => new sfValidatorPass(array('required' => false)),
      'address'            => new sfValidatorPass(array('required' => false)),
      'city'               => new sfValidatorPass(array('required' => false)),
      'post_code'          => new sfValidatorPass(array('required' => false)),
      'comment'            => new sfValidatorPass(array('required' => false)),
      'extra1'             => new sfValidatorPass(array('required' => false)),
      'privacy'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'email_subject'      => new sfValidatorPass(array('required' => false)),
      'email_body'         => new sfValidatorPass(array('required' => false)),
      'ref'                => new sfValidatorPass(array('required' => false)),
      'quota_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Quota'), 'column' => 'id')),
      'quota_emails'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'thank_sent'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'quota_thank_you_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('QuotaThankYou'), 'column' => 'id')),
      'created_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'contact_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Contact', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('petition_signing_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addContactListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.PetitionSigningContact PetitionSigningContact')
      ->andWhereIn('PetitionSigningContact.contact_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'PetitionSigning';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'petition_id'        => 'ForeignKey',
      'fields'             => 'Text',
      'status'             => 'Number',
      'verified'           => 'Number',
      'email'              => 'Text',
      'country'            => 'Text',
      'validation_kind'    => 'Number',
      'validation_data'    => 'Text',
      'delete_code'        => 'Text',
      'widget_id'          => 'ForeignKey',
      'wave_sent'          => 'Number',
      'wave_pending'       => 'Number',
      'wave_cron'          => 'Number',
      'subscribe'          => 'Number',
      'email_hash'         => 'Text',
      'mailed_at'          => 'Date',
      'fullname'           => 'Text',
      'title'              => 'Text',
      'firstname'          => 'Text',
      'lastname'           => 'Text',
      'address'            => 'Text',
      'city'               => 'Text',
      'post_code'          => 'Text',
      'comment'            => 'Text',
      'extra1'             => 'Text',
      'privacy'            => 'Number',
      'email_subject'      => 'Text',
      'email_body'         => 'Text',
      'ref'                => 'Text',
      'quota_id'           => 'ForeignKey',
      'quota_emails'       => 'Number',
      'thank_sent'         => 'Number',
      'quota_thank_you_id' => 'ForeignKey',
      'created_at'         => 'Date',
      'updated_at'         => 'Date',
      'contact_list'       => 'ManyKey',
    );
  }
}
