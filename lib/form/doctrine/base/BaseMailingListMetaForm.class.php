<?php

/**
 * MailingListMeta form base class.
 *
 * @method MailingListMeta getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseMailingListMetaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'mailing_list_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MailingList'), 'add_empty' => false)),
      'kind'            => new sfWidgetFormInputText(),
      'name'            => new sfWidgetFormTextarea(),
      'subst'           => new sfWidgetFormTextarea(),
      'data_json'       => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'mailing_list_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('MailingList'), 'column' => 'id')),
      'kind'            => new sfValidatorInteger(),
      'name'            => new sfValidatorString(),
      'subst'           => new sfValidatorString(),
      'data_json'       => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('mailing_list_meta[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MailingListMeta';
  }

}
