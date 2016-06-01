<?php

/**
 * Quota form base class.
 *
 * @method Quota getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseQuotaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'status'           => new sfWidgetFormInputText(),
      'name'             => new sfWidgetFormInputText(),
      'price'            => new sfWidgetFormInputText(),
      'days'             => new sfWidgetFormInputText(),
      'emails'           => new sfWidgetFormInputText(),
      'user_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'campaign_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => true)),
      'start_at'         => new sfWidgetFormDate(),
      'end_at'           => new sfWidgetFormDate(),
      'paid_at'          => new sfWidgetFormDate(),
      'emails_remaining' => new sfWidgetFormInputText(),
      'order_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Order'), 'add_empty' => true)),
      'upgrade_of_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('UpgradeOf'), 'add_empty' => true)),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'status'           => new sfValidatorInteger(array('required' => false)),
      'name'             => new sfValidatorString(array('max_length' => 120)),
      'price'            => new sfValidatorNumber(),
      'days'             => new sfValidatorInteger(),
      'emails'           => new sfValidatorInteger(),
      'user_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id', 'required' => false)),
      'campaign_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id', 'required' => false)),
      'start_at'         => new sfValidatorDate(array('required' => false)),
      'end_at'           => new sfValidatorDate(array('required' => false)),
      'paid_at'          => new sfValidatorDate(array('required' => false)),
      'emails_remaining' => new sfValidatorInteger(array('required' => false)),
      'order_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Order'), 'column' => 'id', 'required' => false)),
      'upgrade_of_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('UpgradeOf'), 'column' => 'id', 'required' => false)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('quota[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Quota';
  }

}
