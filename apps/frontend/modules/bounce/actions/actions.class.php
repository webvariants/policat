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

  private function deleteCSRFToken($id) {
    return UtilCSRF::gen('delete_signing_bounce', $id, $this->getGuardUser()->getId());
  }

  public function executeDelete(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'));
    /* @var $petition Petition */
    if (!$petition || !$request->isMethod('post')) {
      return $this->notFound();
    }

    if (!$this->getGuardUser()->isDataOwnerOfCampaign($petition->getCampaign())) {
      return $this->noAccess();
    }

    $csrf_token = $this->deleteCSRFToken($petition->getId());

    if ($request->getPostParameter('csrf_token') != $csrf_token) {
      return $this->ajax()->alert('CSRF Attack detected, please relogin.')->render();
    }

    $ids_raw = (array) $request->getPostParameter('ids');
    $ids = array();
    foreach ($ids_raw as $i) {
      if (is_numeric($i)) {
        $ids[] = $i;
      }
    }

    if (!$ids) {
      return $this->notFound();
    }

    $signings = PetitionSigningTable::getInstance()->createQuery('s')
      ->where('s.petition_id = ?', $petition->getId())
      ->andWhere('s.verified = ?', PetitionSigning::VERIFIED_NO)
      ->andWhereIn('s.id', $ids)
      ->execute();

    if (!$signings->count()) {
      return $this->notFound();
    }

    if ($request->getPostParameter('sure') === 'yes') {
      $signings->delete();
      return $this->ajax()
          ->modal('#signing_bounce_delete_modal', 'hide')
          ->remove('#signing_bounce_delete_modal')
          ->replaceWithComponent('#data', 'bounce', 'list', array('petition' => $petition, 'page' => 1))
          ->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('signings' => $signings, 'petition' => $petition, 'csrf_token' => $csrf_token))
        ->modal('#signing_bounce_delete_modal')
        ->render();
  }

}
