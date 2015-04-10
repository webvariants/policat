<?php

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
