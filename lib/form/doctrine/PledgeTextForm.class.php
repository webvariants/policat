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
 * PledgeText form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PledgeTextForm extends BasePledgeTextForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('pledge_text_[%s]');

    $this->setWidget('text', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 30, 'class'=>'markdown')));
    $this->getWidgetSchema()->setLabel('text', false);

    unset($this['pledge_item_id'], $this['petition_text_id']);
  }

}
