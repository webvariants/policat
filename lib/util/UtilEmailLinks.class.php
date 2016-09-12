<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilEmailLinks {

  const VALIDATION = 1;
  const DISCONFIRMATION = 2;
  const REFERER = 3;
  const READMORE = 4;
  const PLEDGE = 5;
  const UNSUBSCRIBE = 6;
  const EDIT = 7;

  static $NAME = array(
      self::VALIDATION => 'Validation URL',
      self::DISCONFIRMATION => 'Disconfirmation URL',
      self::REFERER => 'Referer URL',
      self::READMORE => 'Read more URL',
      self::PLEDGE => 'Pledge URL',
      self::UNSUBSCRIBE => 'Unsubscribe URL',
      self::EDIT => 'Edit URL'
  );
  static $TEXT = array(
      self::VALIDATION => 'Click here to validate',
      self::DISCONFIRMATION => 'Click here to revoke and delete your data',
      self::REFERER => 'Links to referer',
      self::READMORE => 'Read more',
      self::PLEDGE => 'Pledge',
      self::UNSUBSCRIBE => 'Click here to unsubscribe',
      self::EDIT => 'Click here to edit'
  );
  static $SHORTCUT = array(
      self::VALIDATION => '#VALIDATION-URL#', // LARGE BUTTON
      self::DISCONFIRMATION => '#DISCONFIRMATION-URL#', // BUTTON
      self::REFERER => '#REFERER-URL#', // LINK
      self::READMORE => '#READMORE-URL#', // LINK
      self::PLEDGE => '#PLEDGE-URL#', // LARGE BUTTON
      self::UNSUBSCRIBE => '#UNSUBSCRIBE-URL#', // BUTTON
      self::EDIT => '#EDIT-URL#'  // BUTTON
  );
  static $PATH = array(
      self::VALIDATION => array('/validate/', '/widgetval/', '/register/', '/forgotten/'),
      self::DISCONFIRMATION => array('/delete/'),
      self::REFERER => null,
      self::READMORE => null,
      self::PLEDGE => array('/pledge/'),
      self::UNSUBSCRIBE => array('/unsubscribe/'),
      self::EDIT => array('/widgetedit/')
  );

  public static function dataMarkupSet($links = array()) {
    $menu = array();
    foreach ($links as $link) {
      $menu[] = array(
          'name' => self::$NAME[$link],
          'openWith' => '[',
          'placeHolder' => self::$TEXT[$link],
          'closeWith' => '](' . self::$SHORTCUT[$link] . ')'
      );
    }

    return json_encode(array(
        array('separator' => '---------------'),
        array('name' => 'Links', 'className' => 'policat-links', 'dropMenu' => $menu
        )
    ));
  }

  public static function generateEmailCss() {
    $homeurl = sfContext::getInstance()->getRouting()->generate('homepage', array(), true);
    $css = file_get_contents(sfConfig::get('sf_web_dir') . '/css/email.css');
    return strtr($css, array('http://POLICAT-HOST/' => $homeurl));
  }

}
