<?php

/**
 * PetitionContact filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BasePetitionContactFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'secret'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'password'             => new sfWidgetFormFilterInput(),
      'password_reset'       => new sfWidgetFormFilterInput(),
      'password_reset_until' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'comment'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'secret'               => new sfValidatorPass(array('required' => false)),
      'password'             => new sfValidatorPass(array('required' => false)),
      'password_reset'       => new sfValidatorPass(array('required' => false)),
      'password_reset_until' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'comment'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('petition_contact_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PetitionContact';
  }

  public function getFields()
  {
    return array(
      'petition_id'          => 'Number',
      'contact_id'           => 'Number',
      'secret'               => 'Text',
      'password'             => 'Text',
      'password_reset'       => 'Text',
      'password_reset_until' => 'Date',
      'comment'              => 'Text',
    );
  }
}
