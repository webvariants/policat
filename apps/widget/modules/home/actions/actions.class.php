<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * home actions.
 *
 * @package    policat
 * @subpackage home
 * @author     Martin
 */
class homeActions extends sfActions {

  public function executeFeed(sfWebRequest $request) {
    $this->setLayout(false);
    $this->getResponse()->setContentType('application/rss+xml');

    $this->excerpts = array();

    $data = UtilOpenActions::dataByCache();
    if ($data && array_key_exists(UtilOpenActions::RECENT, $data['open'])) {
      $recent = $data['open'][UtilOpenActions::RECENT];
      if ($recent && $recent['excerpts']) {
        $excerpts = $recent['excerpts'];
        $this->excerpts = $excerpts;
      }
    }
  }

}
