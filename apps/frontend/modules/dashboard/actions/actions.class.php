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
 * dashboard actions.
 *
 * @package    policat
 * @subpackage dashboard
 * @author     Martin
 */
class dashboardActions extends policatActions {

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    if (!$this->getUser()->isAuthenticated()) {
        $this->forward('sfGuardAuth', 'signin');
    }

    $this->includeChosen();
    $this->no_campaign = $request->getGetParameter('no_campaign', 0) ? true : false;
  }

  public function executeAdmin(sfWebRequest $request) {

  }

  public function executeStats(sfWebRequest $request) {
//    $this->sent = PetitionSigningContactTable::getInstance()->countSentMails();
//    $this->pending = PetitionSigningContactTable::getInstance()->countPendingMails();
//    $this->outgoing = PetitionSigningContactTable::getInstance()->countOutgoingMails();
    $this->sent = PetitionSigningWaveTable::getInstance()->sumContactStatus(PetitionSigning::STATUS_SENT);
    $this->pending = PetitionSigningWaveTable::getInstance()->sumContactStatus(PetitionSigning::STATUS_PENDING);
    $this->outgoing = PetitionSigningWaveTable::getInstance()->sumContactStatus(PetitionSigning::STATUS_COUNTED);
  }

  public function executeTestmail(sfWebRequest $request) {
    $form = new TestmailForm();

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        UtilMail::send('Testmail', null, $form->getValue('from'), $form->getValue('to'), $form->getValue('subject'), $form->getValue('body'), null, null, null, null, array(), true);

        return $this->ajax()->form($form)->alert('Mail sent.', '', '#testmail', 'after')->render();
      }

      return $this->ajax()->form($form)->render();
    }

    $this->last_bounce = StoreTable::getInstance()->getValueCached(StoreTable::INTERNAL_LAST_TESTING_BOUNCE);

    $this->form = $form;

    $this->includeMarkdown();
  }

}
