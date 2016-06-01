<?php

/**
 * CampaignStore form base class.
 *
 * @method CampaignStore getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseCampaignStoreForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'campaign_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => false)),
      'key'         => new sfWidgetFormInputText(),
      'value'       => new sfWidgetFormTextarea(),
      'language_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'campaign_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id')),
      'key'         => new sfValidatorString(array('max_length' => 80)),
      'value'       => new sfValidatorString(array('required' => false)),
      'language_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'CampaignStore', 'column' => array('campaign_id', 'key_', 'language_id')))
    );

    $this->widgetSchema->setNameFormat('campaign_store[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CampaignStore';
  }

}
