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
 * PledgeItem form base class.
 *
 * @method PledgeItem getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BasePledgeItemForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'petition_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => false)),
      'status'      => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
      'icon'        => new sfWidgetFormInputText(),
      'color'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'petition_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'status'      => new sfValidatorInteger(array('required' => false)),
      'name'        => new sfValidatorString(array('max_length' => 80)),
      'icon'        => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'color'       => new sfValidatorString(array('max_length' => 6, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pledge_item[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PledgeItem';
  }

}
