<?php

/**
 * MediaFile filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseMediaFileFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'petition_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => true)),
      'filename'       => new sfWidgetFormFilterInput(),
      'mimetype'       => new sfWidgetFormFilterInput(),
      'title'          => new sfWidgetFormFilterInput(),
      'extension'      => new sfWidgetFormFilterInput(),
      'size'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'object_version' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'petition_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'filename'       => new sfValidatorPass(array('required' => false)),
      'mimetype'       => new sfValidatorPass(array('required' => false)),
      'title'          => new sfValidatorPass(array('required' => false)),
      'extension'      => new sfValidatorPass(array('required' => false)),
      'size'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'object_version' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('media_file_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MediaFile';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'petition_id'    => 'ForeignKey',
      'filename'       => 'Text',
      'mimetype'       => 'Text',
      'title'          => 'Text',
      'extension'      => 'Text',
      'size'           => 'Number',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
      'object_version' => 'Text',
    );
  }
}
