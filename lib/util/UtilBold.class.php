<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilBold {

  public static function format($text) {
    return preg_replace('/(_)([^_]+)(_)/', '<span style="text-decoration:underline">${2}</span>', $text);
  }

}
