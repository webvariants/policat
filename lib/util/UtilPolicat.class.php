<?php
class UtilPolicat
{
  static function firstString($list, $default = '', $non_empty = true)
  {
    if (is_array($list)) foreach ($list as $entry)
    {
      if ($non_empty)
      {
        if (is_string($entry) && !empty ($entry)) return $entry;
      }
      else
      {
        if (is_string($entry)) return $entry;
      }
    }
    return $default;
  }

  static function style($css = array(), $css2 = null, $css3 = null) {
    if (is_array($css2)) $css = array_merge($css, $css2);
    if (is_array($css3)) $css = array_merge($css, $css3);
    echo ' style="';
    foreach ($css as $key => $value) echo $key . ':' . $value . ';';
    echo '" ';
  }
}