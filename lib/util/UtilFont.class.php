<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilFont {

  public static $FONTS = array(
      '"Helvetica Neue",Helvetica,Arial,sans-serif',
      'Georgia, serif', '"Palatino Linotype", "Book Antiqua", Palatino, serif', '"Times New Roman", Times, serif',
      'Arial, Helvetica, sans-serif', '"Arial Black", Gadget, sans-serif', '"Comic Sans MS", cursive, sans-serif',
      'Impact, Charcoal, sans-serif', '"Lucida Sans Unicode", "Lucida Grande", sans-serif', 'Tahoma, Geneva, sans-serif',
      '"Trebuchet MS", Helvetica, sans-serif', 'Verdana, Geneva, sans-serif', '"Courier New", Courier, monospace',
      '"Lucida Console", Monaco, monospace', '"Lucida Sans Unicode", Verdana, Arial',
      '"Open Sans", sans-serif',
      'Roboto, sans-serif'
  );
  private static $FONT_CSS_FILE = array(
      'Open Sans' => '/fonts/OpenSans/OpenSans.css',
      'Roboto' => '/fonts/Roboto/Roboto.css'
  );

  public static function cssFileByFont($font_family) {
    if (!is_string($font_family)) {
      return null;
    }

    foreach (self::$FONT_CSS_FILE as $i_font_family => $filepath) {
      if (mb_strpos($font_family, $i_font_family, 0, 'UTF-8') !== false) {
        return $filepath;
      }
    }

    return null;
  }

  public static function formOptions($empty = false) {
    $result = array();

    if ($empty) {
      $result[''] = $empty;
    }

    foreach (self::$FONTS as $value) {
      $result[$value] = $value;
    }

    return $result;
  }

}
