<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilMarkdown
{
  public static function transform($text, $xssSafe = true, $lessSafe = false, $extra = false)
  {
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
}