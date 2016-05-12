<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class Util {

  static function enc($text) {
    if ($text === null)
      return '';
    if (is_scalar($text))
      return htmlentities($text, ENT_COMPAT, 'utf-8');
    return '';
  }

  static function parseYoutube($markup) {
    return preg_replace_callback('/%%%([a-zA-Z0-9]+)%%%/i', array('Util', 'youtube'), $markup);
  }

  public static function youtube($id, $width = 300, $height = 210) {
    if (is_array($id))
      $id = $id[1];
    return sprintf('<object type="application/x-shockwave-flash" width="%s" height="%s" data="https://www.youtube.com/v/%s?hl=en&amp;fs=1"><param name="wmode" value="opaque" /><param name="movie" value="https://www.youtube.com/v/%s?hl=en&amp;fs=1"/><param name="allowFullScreen" value="true"/><param name="allowscriptaccess" value="always"/></object>', $width, $height, $id, $id);
  }

  public static function readable_number($number, $decimals = 2, $dec_point = ".", $thousands_sep = ",") {
    if (!is_numeric($number)) {
      return $number;
    }

    if ($number > 9999999) {
      return number_format($number / 1000000000, $decimals, $dec_point, $thousands_sep) . 'G';
    }

    if ($number > 999999) {
      return number_format($number / 1000000, $decimals, $dec_point, $thousands_sep) . 'M';
    }

    if ($number > 999) {
      return number_format($number / 1000, $decimals, $dec_point, $thousands_sep) . 'K';
    }

    return number_format($number, 0, $dec_point, $thousands_sep);
  }
}
