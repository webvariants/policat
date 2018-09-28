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
 * PledgeItem form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PledgeItemForm extends BasePledgeItemForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap4');
    $this->widgetSchema->setNameFormat('pledge_item_' . $this->getObject()->getId() . '_[%s]');

    unset($this['icon']);

    $this->setWidget('status', new sfWidgetFormChoice(array(
        'choices' => PledgeItemTable::$STATUS_SHOW
    )));

    $this->setValidator('stauts', new sfValidatorChoice(array(
        'choices' => array_keys(PledgeItemTable::$STATUS_SHOW),
        'required' => false
    )));

    $colors = range(1, 10);
    foreach ($colors as &$color) {
      $color = 'Color ' . $color;
    }

    $this->setWidget('color', new sfWidgetFormChoice(array('choices' => $colors), array('class' => 'select2-color', 'style' => 'width:220px')));

    $this->setValidator('color', new sfValidatorChoice(array('choices' => array_keys($colors))));

    unset(
      $this['petition_id']
    );
  }

}
