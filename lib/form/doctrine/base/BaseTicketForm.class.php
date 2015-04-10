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
 * Ticket form base class.
 *
 * @method Ticket getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseTicketForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'from_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('From'), 'add_empty' => true)),
      'to_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('To'), 'add_empty' => true)),
      'campaign_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => true)),
      'petition_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => true)),
      'widget_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'add_empty' => true)),
      'target_list_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TargetList'), 'add_empty' => true)),
      'kind'           => new sfWidgetFormInputText(),
      'status'         => new sfWidgetFormInputText(),
      'text'           => new sfWidgetFormTextarea(),
      'data_json'      => new sfWidgetFormTextarea(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'from_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('From'), 'column' => 'id', 'required' => false)),
      'to_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('To'), 'column' => 'id', 'required' => false)),
      'campaign_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id', 'required' => false)),
      'petition_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id', 'required' => false)),
      'widget_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'column' => 'id', 'required' => false)),
      'target_list_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TargetList'), 'column' => 'id', 'required' => false)),
      'kind'           => new sfValidatorInteger(array('required' => false)),
      'status'         => new sfValidatorInteger(array('required' => false)),
      'text'           => new sfValidatorString(array('required' => false)),
      'data_json'      => new sfValidatorString(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ticket[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Ticket';
  }

}
