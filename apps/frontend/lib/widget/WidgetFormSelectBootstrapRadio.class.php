<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetFormSelectBootstrapRadio extends sfWidgetFormSelectRadio {

  protected function formatChoices($name, $value, $choices, $attributes) {
    $buttons = array();
    foreach ($choices as $key => $option) {
      $baseAttributes = array(
          'name' => substr($name, 0, -2),
          'type' => 'radio',
          'value' => self::escapeOnce($key),
          'id' => $id = $this->generateId($name, self::escapeOnce($key)),
      );

      $submit = array(substr($name, 0, -2) => $key);

      $active = strval($key) == strval($value === false ? 0 : $value);
      $radio_attr = array('type' => 'radio', 'autocomplete' => 'off');
      if ($active) {
        $radio_attr['checked'] = 'checked';
      }
      $buttons[] = $this->renderContentTag('label',
      $this->renderContentTag('input', self::escapeOnce($option), $radio_attr),
       array(
          'class' => 'submit submit-propagate btn btn-secondary' . ($active ? ' active' : ''),
          'data-submit' => json_encode($submit)
        ));
    }

    return call_user_func($this->getOption('formatter'), $this, $buttons);
  }

  public function formatter($widget, $buttons) { {
      return !$buttons ? '' : $this->renderContentTag('span', implode($this->getOption('separator'), $buttons), array(
            'data-toggle' => 'buttons',
            'class' => 'btn-group btn-group-toggle ' . $this->getOption('class')));
    }
  }

}
