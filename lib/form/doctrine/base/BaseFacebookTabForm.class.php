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
 * FacebookTab form base class.
 *
 * @method FacebookTab getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseFacebookTabForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'page_id'     => new sfWidgetFormInputText(),
      'language_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'add_empty' => true)),
      'widget_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'page_id'     => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'language_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Language'), 'column' => 'id', 'required' => false)),
      'widget_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'column' => 'id', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'FacebookTab', 'column' => array('page_id', 'language_id')))
    );

    $this->widgetSchema->setNameFormat('facebook_tab[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacebookTab';
  }

}
