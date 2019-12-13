<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilLink
{
  protected static function RouteParamIdCode($route, $param, $id, $code)
  {
    return sfContext::getInstance()->getRouting()->generate($route, array(
      $param => sprintf("%s-%s", str_pad($id, 10, '0', STR_PAD_LEFT), $code)
      ), true);
  }

  public static function signValidation($id, $code)
  {
    return self::RouteParamIdCode('validate', 'code', $id, $code);
  }

  public static function deleteSigning($id, $code)
  {
    return self::RouteParamIdCode('delete_signing', 'code', $id, $code);
  }

  public static function unsubscribeSigning($id, $code)
  {
    return self::RouteParamIdCode('unsubscribe_signing', 'code', $id, $code);
  }

  public static function widgetValidation($id, $code)
  {
    return self::RouteParamIdCode('widgetval', 'code', $id, $code);
  }

  public static function widgetEdit($id, $code)
  {
    return self::RouteParamIdCode('widgetedit', 'code', $id, $code);
  }

  public static function widgetMarkup($id, $followActions = true)
  {
    return
      sprintf
      (
      '<script type="text/javascript" src="%s"></script>',
      sfContext::getInstance()->getRouting()->generate($followActions ? 'api_js_widget' : 'api_js_widget_no_redirect', array('id' => $id), true)
    );
  }
}