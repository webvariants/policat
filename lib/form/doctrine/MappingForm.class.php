<?php

/**
 * Mapping form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MappingForm extends BaseMappingForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('mapping[%s]');

    unset($this['object_version'], $this['id']);
    $this->setWidget('name', new sfWidgetFormInputText());
  }

}
