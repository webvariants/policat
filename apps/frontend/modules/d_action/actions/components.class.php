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
 * @property Petition $petition
 */
class d_actionComponents extends policatComponents {

  public function executeList() {
    $page = isset($this->page) ? $this->page : 1;
    $table = PetitionTable::getInstance();

    if (isset($this->campaign)) {
      $this->form = new FilterPetitionForm(array(), array(
            FilterPetitionForm::USER => $this->getGuardUser(),
            FilterPetitionForm::DELETED_TOO => $this->userIsAdmin()
        ));
      $this->form->bindSelf('c' . $this->campaign->getId());

      $query = $table->queryByCampaign($this->campaign, $this->userIsAdmin());
      $this->petitions = new policatPager($query, $page, 'petition_pager', array('id' => $this->campaign->getId()), true, 20, $this->form);
    } else {
      $this->form = new FilterPetitionForm(array(), array(
            FilterPetitionForm::WITH_CAMPAIGN => true,
            FilterPetitionForm::USER => $this->getGuardUser(),
            FilterPetitionForm::DELETED_TOO => $this->userIsAdmin()
        ));
      $this->form->bindSelf('all');

      $query = $table->queryByUserCampaigns($this->getGuardUser(), $this->userIsAdmin());
      $this->petitions = new policatPager($query, $page, 'petition_pager_all', array(), true, 20, $this->form);
    }

    $this->csrf_token_leave = UtilCSRF::gen('action_leave');
    $this->csrf_token_join = UtilCSRF::gen('action_join');
  }

  public function executeMembers() {
    $this->petition_rights_list = PetitionRightsTable::getInstance()->queryByPetition($this->petition)->execute();
    $this->admin = $this->petition->isCampaignAdmin($this->getGuardUser());
    if (isset($this->no_admin) && $this->no_admin)
      $this->admin = false;
    $this->csrf_token = UtilCSRF::gen('action_members');
  }
  
  public function executeNotice() {
    $this->billingEnabled = StoreTable::value(StoreTable::BILLING_ENABLE);
    $this->campaign = $this->petition->getCampaign();
    $this->follows = $this->petition->getFollowPetitionId() ? $this->petition->getFollowPetition() : null;
    $this->petition_draft = $this->petition->getStatus() == Petition::STATUS_DRAFT;
    $this->petition_text_draft = PetitionTextTable::getInstance()->queryByPetition($this->petition, false, PetitionText::STATUS_DRAFT)->count();
    
  }

  public function executeEditFollow() {
    if ($this->petition->isCampaignAdmin($this->getGuardUser())) {
      $this->form = new EditPetitionFollowForm($this->petition);
    }
  }
  
}