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
 * Download form base class.
 *
 * @method Download getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseDownloadForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'widget_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'add_empty' => true)),
      'petition_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => true)),
      'campaign_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => true)),
      'filename'        => new sfWidgetFormInputText(),
      'filter'          => new sfWidgetFormTextarea(),
      'type'            => new sfWidgetFormInputText(),
      'subscriber'      => new sfWidgetFormInputText(),
      'count'           => new sfWidgetFormInputText(),
      'pages'           => new sfWidgetFormInputText(),
      'pages_processed' => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'widget_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Widget'), 'column' => 'id', 'required' => false)),
      'petition_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id', 'required' => false)),
      'campaign_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id', 'required' => false)),
      'filename'        => new sfValidatorString(array('max_length' => 80)),
      'filter'          => new sfValidatorString(array('required' => false)),
      'type'            => new sfValidatorString(array('max_length' => 40)),
      'subscriber'      => new sfValidatorInteger(array('required' => false)),
      'count'           => new sfValidatorInteger(array('required' => false)),
      'pages'           => new sfValidatorInteger(array('required' => false)),
      'pages_processed' => new sfValidatorInteger(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Download', 'column' => array('filename')))
    );

    $this->widgetSchema->setNameFormat('download[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Download';
  }

}
