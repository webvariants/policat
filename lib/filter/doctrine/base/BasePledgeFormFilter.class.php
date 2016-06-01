<?php

/**
 * Pledge filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BasePledgeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'status'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'status_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'comment'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'status'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'comment'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pledge_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Pledge';
  }

  public function getFields()
  {
    return array(
      'pledge_item_id' => 'Number',
      'contact_id'     => 'Number',
      'status'         => 'Number',
      'status_at'      => 'Date',
      'comment'        => 'Text',
    );
  }
}
