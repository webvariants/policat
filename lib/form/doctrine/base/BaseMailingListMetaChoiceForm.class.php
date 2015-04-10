<?php

/**
 * MailingListMetaChoice form base class.
 *
 * @method MailingListMetaChoice getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseMailingListMetaChoiceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'mailing_list_meta_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MailingListMeta'), 'add_empty' => false)),
      'choice'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'mailing_list_meta_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('MailingListMeta'), 'column' => 'id')),
      'choice'               => new sfValidatorString(array('max_length' => 120)),
    ));

    $this->widgetSchema->setNameFormat('mailing_list_meta_choice[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MailingListMetaChoice';
  }

}
