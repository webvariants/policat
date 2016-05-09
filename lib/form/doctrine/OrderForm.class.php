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
 * Order form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class OrderForm extends BaseOrderForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('order[%s]');

    unset($this['created_at'], $this['updated_at'], $this['user_id'], $this['campaign_id'], $this['tax'], $this['paypal_payment_id'], $this['paypal_sale_id'], $this['paypal_status']);
  }

}
