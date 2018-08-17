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
 * Product form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
class ProductForm extends BaseProductForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('product[%s]');

    unset($this['object_version']);

    $this->getWidgetSchema()->setLabel('emails', 'E-mails total');
    $this->getWidgetSchema()->setLabel('price', 'Price (net)');

    if (StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_ENABLE)) {
      $this->setWidget('subscription', new sfWidgetFormChoice(array(
          'choices' => array(0 => 'no', 1 => 'yes'),
          'label' => 'Subscription / Abo'
        ), array(
      )));
      $this->setValidator('subscription', new sfValidatorChoice(array('choices' => array(0, 1), 'required' => true)));
    } else {
      unset($this['subscription']);
    }
  }

}
