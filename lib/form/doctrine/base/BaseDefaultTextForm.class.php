<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * DefaultText form base class.
 *
 * @method DefaultText getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseDefaultTextForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'language_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => false)),
      'text'        => new sfWidgetFormInputText(),
      'subject'     => new sfWidgetFormInputText(),
      'body'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'language_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id')),
      'text'        => new sfValidatorString(array('max_length' => 20)),
      'subject'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'body'        => new sfValidatorString(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DefaultText', 'column' => array('language_id', 'text')))
    );

    $this->widgetSchema->setNameFormat('default_text[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DefaultText';
  }

}
