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
 * Quota form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
class QuotaForm extends BaseQuotaForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('quota[%s]');

    unset($this['created_at'], $this['updated_at'], $this['user_id'], $this['campaign_id'], $this['order_id'], $this['upgrade_of_id'], $this['subscription'], $this['renew_offerred'], $this['product_id']);


    $this->setWidget('start_at', new sfWidgetFormInput(array('type' => 'date')));
    $this->setWidget('end_at', new sfWidgetFormInput(array('type' => 'date')));
    $this->setWidget('paid_at', new sfWidgetFormInput(array('type' => 'date')));

    $this->setWidget('status', new sfWidgetFormChoice(array(
        'choices' => QuotaTable::$STATUS_SHOW
    )));
    $this->setValidator('status', new sfValidatorChoice(
      array('choices' => array_keys(QuotaTable::$STATUS_SHOW)
    )));

    if (StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_ENABLE) && $this->getObject()->getProductId() && $this->getObject()->getProduct()->getSubscription()) {
      $this->setWidget('subscription', new sfWidgetFormChoice(array(
          'choices' => array(0 => 'no', 1 => 'yes'),
          'label' => 'Subscription'
        ), array(
      )));
      $this->setValidator('subscription', new sfValidatorChoice(array('choices' => array(0, 1), 'required' => true)));
    }
  }

}
