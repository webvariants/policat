<?php

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

      $buttons[] = $this->renderContentTag('button', self::escapeOnce($option), array(
          'class' => 'btn' . ((strval($key) == strval($value === false ? 0 : $value)) ? ' active' : ''),
          'data-submit' => json_encode($submit)
        ));

//      $buttons[$id] = array(
//        'input' => $this->renderTag('input', array_merge($baseAttributes, $attributes)),
//        'label' => $this->renderContentTag('label', self::escapeOnce($option), array('for' => $id)),
//      );
    }

    return call_user_func($this->getOption('formatter'), $this, $buttons);
  }

  public function formatter($widget, $buttons) { {
      return !$buttons ? '' : $this->renderContentTag('span', implode($this->getOption('separator'), $buttons), array(
            'data-toggle' => 'buttons-radio',
            'class' => 'btn-group ' . $this->getOption('class')));
    }
  }

}

