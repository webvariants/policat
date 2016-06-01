<?php

/**
 * PrivacyPolicy form base class.
 *
 * @method PrivacyPolicy getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BasePrivacyPolicyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'language_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => false)),
      'body'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'language_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id')),
      'body'        => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('privacy_policy[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PrivacyPolicy';
  }

}
