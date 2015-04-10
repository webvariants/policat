<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sfWidgetFormSchemaFormatterPolicat extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<div class=\"form_row\">%error%%label%\n  %field%%help%\n%hidden_fields%</div>",
    $errorRowFormat  = "<span class=\"error\">\n%errors%</span>\n",
    $helpFormat      = '<span class="help">%help%</span>',
    $decoratorFormat = "<div>\n  %content%</div>";
}
