<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sfWidgetFormSelectProduct extends sfWidgetFormSelectRadio {

  protected function formatChoices($name, $value, $choices, $attributes) {
    $inputs = array();
    $subscription = StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_ENABLE);
    foreach ($choices as $key => $option) {
      $baseAttributes = array(
          'name' => substr($name, 0, -2),
          'type' => 'radio',
          'value' => self::escapeOnce($key),
          'class' => 'top0'
      );

      $id = $this->generateId($name, self::escapeOnce($key));

      if (strval($key) == strval($value === false ? 0 : $value)) {
        $baseAttributes['checked'] = 'checked';
      }

      use_helper('Number');
      if ($option instanceof Product) {
        $baseAttributes['id'] = $id;
        $sub_extra = '';
        if ($subscription) {
            $sub_extra = '<td>' . ($option->getSubscription() ? 'yes' : 'no') .  '</td>';
        }
        $inputs[$id] = sprintf('<tr><td><label style="cursor:pointer">%s %s</label></td><td style="text-align: right;">%s</td>' . $sub_extra . ' <td style="text-align: right;">%s</td><td style="text-align: right;">%s</td><td style="text-align: right;">%s</td></tr>', $this->renderTag('input', array_merge($baseAttributes, $attributes)), self::escapeOnce($option->getName()), format_number($option->getEmails()), format_number($option->getDays()), format_currency($option->getPrice(), StoreTable::value(StoreTable::BILLING_CURRENCY)), format_currency($option->getPriceBrutto(), StoreTable::value(StoreTable::BILLING_CURRENCY)));
      }
    }

    return call_user_func($this->getOption('formatter'), $this, $inputs, $name);
  }

  public function formatter($widget, $inputs, $name = null) {
    $rows = array();
    foreach ($inputs as $input) {
      $rows[] = $this->renderContentTag('div', $input, array('class' => 'row'));
    }

    $subscription = StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_ENABLE);
    $sub_extra = '';
    if ($subscription) {
        $sub_extra = '<th class="span2">Subscription only</th>';
    }

    return !$rows ? '' : $this->renderContentTag('table', '<tr><th>Package</th><th class="span3">E-mails / participants</th>' . $sub_extra . '<th class="span2">Days</th><th class="span2">Net</th><th class="span2">Gross</th></tr>' .
        $this->renderContentTag('tbody', implode('', $rows), array('class' => '')), array('class' => 'table table-bordered', 'id' => $this->generateId($name)));
  }

}
