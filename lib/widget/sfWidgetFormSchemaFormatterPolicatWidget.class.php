<?php


class sfWidgetFormSchemaFormatterPolicatWidget extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "%error%%label%%help%\n  %field%\n%hidden_fields%",
    $errorRowFormat  = "<span class=\"error\">\n%errors%</span>\n",
    $helpFormat      = '<span class="help"> (%help%)</span>',
    $decoratorFormat = "<div>\n  %content%</div>";
}
