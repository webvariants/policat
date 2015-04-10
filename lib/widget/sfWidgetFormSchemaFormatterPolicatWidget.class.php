<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
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
}
