<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * main actions.
 *
 * @package    policat
 * @subpackage main
 * @author     Martin
 */
class mainActions extends sfActions
{
  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
  }

  public function executePage(sfWebRequest $request)
  {
    $this->id = $request->getParameter('id', 1);
    $this->setLayout(false);
  }
}
