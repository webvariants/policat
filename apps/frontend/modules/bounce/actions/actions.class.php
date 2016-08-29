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
 * bounce actions.
 *
 * @author     Martin
 */
class bounceActions extends policatActions {

  public function executeList(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'));
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$this->getGuardUser()->isDataOwnerOfCampaign($petition->getCampaign())) {
      return $this->noAccess();
    }

    $this->page = (int) $request->getParameter('page');
    $this->petition = $petition;
  }

  public function executePager(sfWebRequest $request) {
    if ($ret = $this->executeList($request)) {
      return $ret;
    }

    return $this->ajax()->replaceWithComponent('#data', 'bounce', 'list', array('petition' => $this->petition, 'page' => $this->page))->render();
  }

}
