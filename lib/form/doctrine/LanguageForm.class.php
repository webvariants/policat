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
 * Language form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LanguageForm extends BaseLanguageForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('language[%s]');

    unset($this['object_version'], $this['active']);

    $this->setWidget('name', new sfWidgetFormInputText());

    if ($this->getObject()->isNew()) {
      $this->setWidget('id', new sfWidgetFormInputText(array('label' => 'ISO Code')));
      $this->setValidator('id', new sfValidatorRegex(array('pattern' => '/^[a-z][a-z](_[A-Z][A-Z])?$/')));
    }
    else
      unset($this['id']);
  }

}
