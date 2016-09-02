<?php

/**
 * Download filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseDownloadFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'widget_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'add_empty' => true)),
      'petition_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => true)),
      'campaign_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => true)),
      'filename'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'filter'          => new sfWidgetFormFilterInput(),
      'type'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'subscriber'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'count'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'pages'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'pages_processed' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'incremental'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'user_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'widget_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Widget'), 'column' => 'id')),
      'petition_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'campaign_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Campaign'), 'column' => 'id')),
      'filename'        => new sfValidatorPass(array('required' => false)),
      'filter'          => new sfValidatorPass(array('required' => false)),
      'type'            => new sfValidatorPass(array('required' => false)),
      'subscriber'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'count'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'pages'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'pages_processed' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'incremental'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('download_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Download';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'user_id'         => 'ForeignKey',
      'widget_id'       => 'ForeignKey',
      'petition_id'     => 'ForeignKey',
      'campaign_id'     => 'ForeignKey',
      'filename'        => 'Text',
      'filter'          => 'Text',
      'type'            => 'Text',
      'subscriber'      => 'Number',
      'count'           => 'Number',
      'pages'           => 'Number',
      'pages_processed' => 'Number',
      'incremental'     => 'Number',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
    );
  }
}
