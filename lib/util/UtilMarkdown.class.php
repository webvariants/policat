<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilMarkdown {

  public static function transform($text, $xssSafe = true, $lessSafe = false, $extra = false) {
    if (strlen($text) <= 1) {
      return '';
    }

    if ($extra) {
      $html = \Michelf\MarkdownExtra::defaultTransform($text);
    } else {
      $html = \Michelf\Markdown::defaultTransform($text);
    }
    if ($xssSafe) {
      $html = UtilHtmlPurifier::xssSafe($html, $lessSafe);
    }
    return $html;
  }

  public static function transformSubst($text, $subst_escape, $xssSafe = true, $lessSafe = false, $extra = false) {
    $hash = mt_rand(0, mt_getrandmax());

    if ($subst_escape && is_array($subst_escape)) {
      $forth = array();
      $back = array();
      $i = 0;

      foreach ($subst_escape as $subst_key => $subst_value) {
        $i++;
        $forth[$subst_key] = 'PC123p0LiC4t' . $hash . $i . 'PC123';
        $back[$forth[$subst_key]] = Util::enc($subst_value);
      }

      return strtr(self::transform(strtr($text, $forth), $xssSafe, $lessSafe, $extra), $back);
    } else {
      return self::transform($text, $xssSafe, $lessSafe, $extra);
    }
  }

  public static function transformMedia($text, $petition, $xssSafe = true, $lessSafe = false, $extra = false) {

    $subst_escape = MediaFileTable::getInstance()->substInternalToExternal($petition);

    return self::transformSubst($text, $subst_escape, $xssSafe, $lessSafe, $extra);
  }

}
