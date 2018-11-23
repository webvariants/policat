<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sfWidgetFormSchemaFormatterBootstrap extends sfWidgetFormSchemaFormatter {

  protected
    $rowFormat = '<div class="form-group">%label% %field% %help% %error% %hidden_fields%</div>',
    $rowFormatCheckbox = '<div class="form-check">%field% %label% %help% %error% %hidden_fields%</div>',
    $errorRowFormat = '%errors%',
    $errorListFormatInARow     = "%errors%",
    $errorRowFormatInARow      = '<div class="invalid-feedback">%error%</div>',
    $namedErrorRowFormatInARow = '<div class="invalid-feedback">%name%: %error%</div>',
    $helpFormat = '<p class="help-block form-text">%help%</p>',
    $decoratorFormat = "<div>\n  %content%</div>";

  protected $checkboxes = array();

  public function __construct(sfWidgetFormSchema $widgetSchema) {
    parent::__construct($widgetSchema);

    foreach ($widgetSchema->getPositions() as $name)
    {
      $field = $widgetSchema[$name];
      $addClass = 'form-control';
      if ($field instanceof sfWidgetFormInputCheckbox) {
        $addClass = 'form-check-input';
        $this->checkboxes[] = $name;
      }
      if ($field instanceof sfWidgetFormChoice) {
          if ($field->getOption('multiple')) {
              $addClass = '';
          }
      }

      if ($addClass) {
        $class = $field->getAttribute('class');
        $field->setAttribute('class', ($class ? $class . ' ' : '') . $addClass);
      }
    }
  }

  public function generateLabel($name, $attributes = array()) {
    $labelName = $this->generateLabelName($name);

    if (false === $labelName) {
      return '';
    }

    if (!isset($attributes['for'])) {
      $attributes['for'] = $this->widgetSchema->generateId($this->widgetSchema->generateName($name));
    }

    if (in_array($name, $this->checkboxes)) {
      $attributes['class'] = 'form-check-label';
    } else {
      $attributes['class'] = 'control-label';
    }

    return $this->widgetSchema->renderContentTag('label', $labelName, $attributes);
  }

  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    if ($label && strpos($label, '<label') === false) {
      // it is a label for an embedded form
      $label = '<label class="control-label">' . $label . '</label>';
    }

    if ($errors) {
      $field = preg_replace('/form-control/', 'form-control is-invalid', $field, 1);
    }

    $isCheckbox = is_string($field) && strpos($field, 'form-check-input') !== false && strpos($field, 'type="file"') === false;

    return strtr($isCheckbox ? $this->getRowFormatCheckbox() : $this->getRowFormat(), array(
      '%label%'         => $label,
      '%field%'         => $field,
      '%error%'         => $this->formatErrorsForRow($errors),
      '%help%'          => $this->formatHelp($help),
      '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
    ));
  }

  public function getRowFormatCheckbox()
  {
    return $this->rowFormatCheckbox;
  }
}
