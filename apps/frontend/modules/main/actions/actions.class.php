<?php

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
