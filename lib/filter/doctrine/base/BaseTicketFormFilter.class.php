<?php

/**
 * Ticket filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseTicketFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'from_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('From'), 'add_empty' => true)),
      'to_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('To'), 'add_empty' => true)),
      'campaign_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => true)),
      'petition_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => true)),
      'widget_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'add_empty' => true)),
      'target_list_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TargetList'), 'add_empty' => true)),
      'kind'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'status'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'text'           => new sfWidgetFormFilterInput(),
      'data_json'      => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'from_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('From'), 'column' => 'id')),
      'to_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('To'), 'column' => 'id')),
      'campaign_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Campaign'), 'column' => 'id')),
      'petition_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'widget_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Widget'), 'column' => 'id')),
      'target_list_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TargetList'), 'column' => 'id')),
      'kind'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'text'           => new sfValidatorPass(array('required' => false)),
      'data_json'      => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ticket_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Ticket';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'from_id'        => 'ForeignKey',
      'to_id'          => 'ForeignKey',
      'campaign_id'    => 'ForeignKey',
      'petition_id'    => 'ForeignKey',
      'widget_id'      => 'ForeignKey',
      'target_list_id' => 'ForeignKey',
      'kind'           => 'Number',
      'status'         => 'Number',
      'text'           => 'Text',
      'data_json'      => 'Text',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
    );
  }
}
