<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sfWidgetFormSchemaFormatterBootstrapInline extends sfWidgetFormSchemaFormatter {

  protected
    $rowFormat = '%label%%field%%help%%error%%hidden_fields%',
    $errorRowFormat = '<p class="help-block">%errors%</p>',
    $helpFormat = '<p class="help-block">%help%<p>',
    $decoratorFormat = "<div>\n  %content%</div>";

}

