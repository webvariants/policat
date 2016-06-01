<?php

/**
 * ApiTokenOffset form base class.
 *
 * @method ApiTokenOffset getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseApiTokenOffsetForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'petition_api_token_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ApiToken'), 'add_empty' => false)),
      'country'               => new sfWidgetFormInputText(),
      'addnum'                => new sfWidgetFormInputText(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'petition_api_token_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ApiToken'), 'column' => 'id')),
      'country'               => new sfValidatorString(array('max_length' => 5)),
      'addnum'                => new sfValidatorInteger(array('required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('api_token_offset[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ApiTokenOffset';
  }

}
