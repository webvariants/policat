<?php

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
    $this->outgoing = PetitionSigningWaveTable::getInstance()->sumContactStatus(PetitionSigning::STATUS_VERIFIED);
  }

  public function executeTestmail(sfWebRequest $request) {
    $form = new TestmailForm();

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        UtilMail::send(null, $form->getValue('from'), $form->getValue('to'), $form->getValue('subject'), $form->getValue('body'));
        
        return $this->ajax()->form($form)->alert('Mail sent.', '', '#testmail', 'after')->render();
      }

      return $this->ajax()->form($form)->render();
    }

    $this->form = $form;
  }

}
