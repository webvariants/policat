<?php

/**
 * PetitionApiToken form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
class PetitionApiTokenForm extends BasePetitionApiTokenForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('api_token[%s]');

    unset($this['petition_id'], $this['created_at'], $this['updated_at']);

    $this->setWidget('status', new sfWidgetFormChoice(array(
        'choices' => PetitionApiTokenTable::$STATUS
    )));

    $this->setValidator('status', new sfValidatorChoice(array(
        'choices' => array_keys(PetitionApiTokenTable::$STATUS)
    )));

    $this->getWidgetSchema()->setHelp('token', '30 characters min');
    $this->getValidator('token')->setOption('min_length', 30);
    $this->getValidatorSchema()->getPostValidator()->setMessage('invalid', 'Token not unique.');
  }

}
