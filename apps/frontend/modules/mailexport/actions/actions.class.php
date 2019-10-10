<?php

/*
 * Copyright (c) 2019, webvariants GmbH, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * dashboard action actions.
 *
 * @author     Martin
 */
class mailexportActions extends policatActions {

  public function executeSetting(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'));
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$this->getGuardUser()->isDataOwnerOfCampaign($petition->getCampaign())) {
      return $this->noAccess();
    }

    $form = new MailExportSettingForm($petition->getMailexportData());
    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));

      if ($form->isValid()) {

        $petition->setMailexportData($form->getValues());
        $petition->setMailexportEnabled(MailExport::checkOneEnabled($petition) ? 1 : 0);
        $petition->save();
        return $this->ajax()->redirectRotue('petition_overview', array('id' => $petition->getId()))->render();

      } else {
        return $this->ajax()->form($form)->render();
      }
    }
  }

  public function executeTest(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'));
    /* @var $petition Petition */
    if (!$petition) {
      return $this->notFound();
    }

    if (!$this->getGuardUser()->isDataOwnerOfCampaign($petition->getCampaign())) {
      return $this->noAccess();
    }

    $this->petition = $petition;

    $csrf_token = UtilCSRF::gen('mailexport_test', $petition->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();
      }
    }

    $name = $request->getPostParameter('service');
    $service = Mailexport::getService($name);
    if (!$service) {
      return $this->ajax()->alert('Service not found.', 'Error')->render();
    }

    $test = $service->test($petition);

    if ($test['status']) {
      return $this->ajax()->alert($test['message'], 'Success', '#test-' . $name, 'append')->render();
    } else {
      return $this->ajax()->alert($test['message'], 'Failed', '#test-' . $name, 'append')->render();
    }
  }
}