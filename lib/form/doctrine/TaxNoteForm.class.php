<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * TaxNote form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
class TaxNoteForm extends BaseTaxNoteForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('taxnote[%s]');

    unset($this['object_version']);

    $this->getWidget('name')->setAttribute('class', 'span7');
    $this->getWidgetSchema()->setHelp('name', 'This name is only shown in admin backend.');
    $this->setWidget('note', new sfWidgetFormInputText(array(), array('class' => 'span7')));
    $this->getWidgetSchema()->setHelp('note', 'Text shown in order, offer and bill.');
  }

}
