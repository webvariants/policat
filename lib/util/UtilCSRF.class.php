<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilCSRF {

  protected static function user() {
    return session_id();
  }

  protected static function secret() {
    return sfConfig::get('app_frontend_csrf_secret');
  }

  protected static function hash($data) {
    return base_convert(sha1($data), 16, 36);
  }

  public static function gen($related_data = null) {
    if (func_num_args() > 1) {
      $related_data = func_get_args();
    }
    return self::hash(self::secret() . '_' . self::user() . '_' . ($related_data === null ? '' : json_encode($related_data)));
  }

}
