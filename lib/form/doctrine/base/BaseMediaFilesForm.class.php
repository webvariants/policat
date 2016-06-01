<?php

/**
 * MediaFiles form base class.
 *
 * @method MediaFiles getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseMediaFilesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'petition_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => false)),
      'filename'    => new sfWidgetFormInputText(),
      'title'       => new sfWidgetFormInputText(),
      'path'        => new sfWidgetFormInputText(),
      'extention'   => new sfWidgetFormInputText(),
      'size'        => new sfWidgetFormInputText(),
      'sort_order'  => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'petition_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'filename'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'title'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'path'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'extention'   => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'size'        => new sfValidatorInteger(array('required' => false)),
      'sort_order'  => new sfValidatorInteger(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('media_files[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MediaFiles';
  }

}
