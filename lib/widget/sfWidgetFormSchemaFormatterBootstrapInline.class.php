<?php

class sfWidgetFormSchemaFormatterBootstrapInline extends sfWidgetFormSchemaFormatter {

  protected
    $rowFormat = '%label%%field%%help%%error%%hidden_fields%',
    $errorRowFormat = '<p class="help-block">%errors%</p>',
    $helpFormat = '<p class="help-block">%help%<p>',
    $decoratorFormat = "<div>\n  %content%</div>";

}

