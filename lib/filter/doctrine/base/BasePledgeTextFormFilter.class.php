<?php

/**
 * PledgeText filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BasePledgeTextFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'text'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'text'             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pledge_text_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PledgeText';
  }

  public function getFields()
  {
    return array(
      'pledge_item_id'   => 'Number',
      'petition_text_id' => 'Number',
      'text'             => 'Text',
    );
  }
}
