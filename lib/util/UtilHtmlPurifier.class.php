<?php

class UtilHtmlPurifier {

  protected static $purifier = null;
  protected static $purifier_less_safe = null;

  public static function xssSafe($html, $lessSafe = false) {
    require_once sfConfig::get('sf_lib_dir') . '/vendor/htmlpurifier/HTMLPurifier.standalone.php';

    if ($lessSafe) {
      if (!isset(self::$purifier_less_safe)) {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.SafeObject', true);
        $config->set('Output.FlashCompat', true);
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^https://(www.youtube.com/embed)|(player.vimeo.com)/%');
        
        self::$purifier_less_safe = new HTMLPurifier($config);
      }

      return self::$purifier_less_safe->purify($html);
    }

    if (!isset(self::$purifier))
      self::$purifier = new HTMLPurifier();

    return self::$purifier->purify($html);
  }

}