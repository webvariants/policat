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
    $rowFormat = '<div class="control-group">%label%<div class="controls">%field%%help%%error%</div>%hidden_fields%</div>',
    $errorRowFormat = '<p class="help-block">%errors%</p>',
    $helpFormat = '<p class="help-block">%help%</p>',
    $decoratorFormat = "<div>\n  %content%</div>";

  public function generateLabel($name, $attributes = array()) {
    $labelName = $this->generateLabelName($name);

    if (false === $labelName) {
      return '';
    }

    if (!isset($attributes['for'])) {
      $attributes['for'] = $this->widgetSchema->generateId($this->widgetSchema->generateName($name));
    }
    
    $attributes['class'] = 'control-label';

    return $this->widgetSchema->renderContentTag('label', $labelName, $attributes);
  }

  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    if ($label && strpos($label, '<label') === false) {
      // it is a label for an embedded form
      $label = '<label class="control-label">' . $label . '</label>';
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

