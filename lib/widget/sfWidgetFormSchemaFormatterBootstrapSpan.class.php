<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sfWidgetFormSchemaFormatterBootstrapSpan extends sfWidgetFormSchemaFormatter {

  protected
    $rowFormat = '<span>%label% %field% %help% %error% %hidden_fields%</span>',
    $errorRowFormat = '%errors%',
    $errorListFormatInARow     = "%errors%",
    $errorRowFormatInARow      = '<div class="invalid-feedback">%error%</div>',
    $namedErrorRowFormatInARow = '<div class="invalid-feedback">%name%: %error%</div>',
    $helpFormat = '<p class="help-block form-text">%help%</p>',
    $decoratorFormat = "<div>\n  %content%</div>";

  public function __construct(sfWidgetFormSchema $widgetSchema) {
    parent::__construct($widgetSchema);

    foreach ($widgetSchema->getFields() as $field)
    {
      $addClass = 'form-control form-control-sm';
      if ($field instanceof sfWidgetFormInputCheckbox) {
        $addClass = 'form-check';
      }
      $class = $field->getAttribute('class');
      $field->setAttribute('class', ($class ? $class . ' ' : '') . $addClass);
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

    $attributes['class'] = 'control-label small';

    return $this->widgetSchema->renderContentTag('label', $labelName, $attributes);
  }

  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    if ($label && strpos($label, '<label') === false) {
      // it is a label for an embedded form
      $label = '<label class="control-label small">' . $label . '</label>';
    }

    if ($errors) {
      $field = preg_replace('/form-control/', 'form-control is-invalid', $field, 1);
    }

    return strtr($this->getRowFormat(), array(
      '%label%'         => $label,
      '%field%'         => $field,
      '%error%'         => $this->formatErrorsForRow($errors),
      '%help%'          => $this->formatHelp($help),
      '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
    ));
  }

}
