<?php

/**
 * Contact filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseContactFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'status'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mailing_list_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MailingList'), 'add_empty' => true)),
      'email'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gender'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'firstname'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'lastname'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'country'               => new sfWidgetFormFilterInput(),
      'language_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => true)),
      'bounce'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'bounce_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'bounce_blocked'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'bounce_hard'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'bounce_related_to'     => new sfWidgetFormFilterInput(),
      'bounce_error'          => new sfWidgetFormFilterInput(),
      'petition_signing_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'PetitionSigning')),
    ));

    $this->setValidators(array(
      'status'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'mailing_list_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('MailingList'), 'column' => 'id')),
      'email'                 => new sfValidatorPass(array('required' => false)),
      'gender'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'firstname'             => new sfValidatorPass(array('required' => false)),
      'lastname'              => new sfValidatorPass(array('required' => false)),
      'country'               => new sfValidatorPass(array('required' => false)),
      'language_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Language'), 'column' => 'id')),
      'bounce'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bounce_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'bounce_blocked'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bounce_hard'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bounce_related_to'     => new sfValidatorPass(array('required' => false)),
      'bounce_error'          => new sfValidatorPass(array('required' => false)),
      'petition_signing_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'PetitionSigning', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('contact_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addPetitionSigningListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('PetitionSigningContact.petition_signing_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Contact';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'status'                => 'Number',
      'mailing_list_id'       => 'ForeignKey',
      'email'                 => 'Text',
      'gender'                => 'Number',
      'firstname'             => 'Text',
      'lastname'              => 'Text',
      'country'               => 'Text',
      'language_id'           => 'ForeignKey',
      'bounce'                => 'Number',
      'bounce_at'             => 'Date',
      'bounce_blocked'        => 'Number',
      'bounce_hard'           => 'Number',
      'bounce_related_to'     => 'Text',
      'bounce_error'          => 'Text',
      'petition_signing_list' => 'ManyKey',
    );
  }
}
