<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */


class sfWidgetFormSchemaFormatterPolicatWidget extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "%error%%label%%help%\n  %field%\n%hidden_fields%",
    $errorRowFormat  = "<span class=\"error\">\n%errors%</span>\n",
    $helpFormat      = '<span class="help"> (%help%)</span>',
    $decoratorFormat = "<div>\n  %content%</div>";

    /**
   * Generates a label for the given field name.
   *
   * @param  string $name        The field name
   * @param  array  $attributes  Optional html attributes for the label tag
   *
   * @return string The label tag
   */
  public function generateLabel($name, $attributes = array())
  {
    $labelName = $this->generateLabelName($name);

    if (false === $labelName)
    {
      return '';
    }

    if (!isset($attributes['for']))
    {
      $attributes['for'] = $this->widgetSchema->generateId($this->widgetSchema->generateName($name));
    }

    if ($name === Petition::FIELD_PRIVACY) {
      $with_spans = preg_replace('/(_)([^_]+)(_)/', '<span class="label-link">${2}</span>', $labelName, -1, $replace_count);
      if ($replace_count) {
        $labelName = $with_spans;
      } else {
        $labelName = '<span class="label-link">' . $labelName . '</span>';
      }
    }

    return $this->widgetSchema->renderContentTag('label', $labelName, $attributes);
  }
}
