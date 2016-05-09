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
    $rowFormat = '<span class="form_row">%label%%field%%help%%error%%hidden_fields%</span>',
    $errorRowFormat = '<p class="help-block">%errors%</p>',
    $helpFormat = '<p class="help-block">%help%<p>',
    $decoratorFormat = "<div>\n  %content%</div>";

}

