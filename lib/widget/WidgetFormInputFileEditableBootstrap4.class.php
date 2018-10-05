<?php

class WidgetFormInputFileEditableBootstrap4 extends sfWidgetFormInputFileEditable {
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('template', '%file%<br />%input%<div class="form-check">%delete%%delete_label%</div>');
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $input = sfWidgetFormInputFile::render($name, $value, $attributes, $errors);

    if (!$this->getOption('edit_mode'))
    {
      return $input;
    }

    if ($this->getOption('with_delete'))
    {
      $deleteName = ']' == substr($name, -1) ? substr($name, 0, -1).'_delete]' : $name.'_delete';

      $delete = $this->renderTag('input', array_merge(array('type' => 'checkbox', 'name' => $deleteName), array_merge($attributes, array('class' => 'form-check-input'))));
      $deleteLabel = $this->translate($this->getOption('delete_label'));
      $deleteLabel = $this->renderContentTag('label', $deleteLabel, array_merge(array('for' => $this->generateId($deleteName), 'class' => 'form-check-label')));
    }
    else
    {
      $delete = '';
      $deleteLabel = '';
    }

    return strtr($this->getOption('template'), array('%input%' => $input, '%delete%' => $delete, '%delete_label%' => $deleteLabel, '%file%' => $this->getFileAsTag($attributes)));
  }
}
