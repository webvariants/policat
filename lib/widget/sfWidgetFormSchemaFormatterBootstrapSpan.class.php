<?php

class sfWidgetFormSchemaFormatterBootstrapSpan extends sfWidgetFormSchemaFormatter {

  protected
    $rowFormat = '<span class="form_row">%label%%field%%help%%error%%hidden_fields%</span>',
    $errorRowFormat = '<p class="help-block">%errors%</p>',
    $helpFormat = '<p class="help-block">%help%<p>',
    $decoratorFormat = "<div>\n  %content%</div>";

}

