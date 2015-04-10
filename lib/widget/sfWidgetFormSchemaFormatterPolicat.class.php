<?php


class sfWidgetFormSchemaFormatterPolicat extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<div class=\"form_row\">%error%%label%\n  %field%%help%\n%hidden_fields%</div>",
    $errorRowFormat  = "<span class=\"error\">\n%errors%</span>\n",
    $helpFormat      = '<span class="help">%help%</span>',
    $decoratorFormat = "<div>\n  %content%</div>";
}
