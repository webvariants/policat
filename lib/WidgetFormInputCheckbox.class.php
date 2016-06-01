<?php

class WidgetFormInputCheckbox extends sfWidgetFormInput {

  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('value_attribute_value');
    $this->addOption('value_unchecked', null);
    $this->addOption('value_checked', null);

    parent::__construct($options, $attributes);
  }

  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('type', 'checkbox');

    if (isset($attributes['value']))
    {
      $this->setOption('value_attribute_value', $attributes['value']);
    }
  }

  public function render($name, $value = null, $attributes = array(), $errors = array()) {

    if (!isset($attributes['value']) && null !== $this->getOption('value_attribute_value'))
    {
      $attributes['value'] = $this->getOption('value_attribute_value');
    }

    $value_checked = $this->getOption('value_checked');
    if ($value_checked !== null && $value_checked == $value) {
      $attributes['checked'] = true;
    }

    $value_unchecked = $this->getOption('value_unchecked');
    $before = '';

    if ($value_unchecked !== null) {
      $before = $this->renderTag('input', array_merge(array('type' => 'hidden', 'name' => $name, 'value' => $value_unchecked)));
    }

    return $before . parent::render($name, null, $attributes, $errors);
  }
}