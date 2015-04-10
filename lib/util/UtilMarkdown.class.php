<?php

class UtilMarkdown
{
  public static function transform($text, $xssSafe = true, $lessSafe = false)
  {
    include_once sfConfig::get('sf_lib_dir') . '/vendor/markdown/markdown.php';
    $html = Markdown($text);
    if ($xssSafe) $html = UtilHtmlPurifier::xssSafe($html, $lessSafe);
    return $html;
  }
}