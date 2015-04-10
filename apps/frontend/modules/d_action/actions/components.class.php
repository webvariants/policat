<?php

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

      $query = $table->queryByUserCampaigns($this->getGuardUser(), false, false);
      $this->petitions = new policatPager($query, $page, 'petition_pager_all', array(), true, 20, $this->form);
    }

    $this->csrf_token_leave = UtilCSRF::gen('action_leave');
    $this->csrf_token_join = UtilCSRF::gen('action_join');
  }

  public function executeMembers() {
    $this->petition_rights_list = PetitionRightsTable::getInstance()->queryByPetition($this->petition)->execute();
    $this->admin = $this->petition->isMemberEditable($this->getGuardUser());
    if (isset($this->no_admin) && $this->no_admin)
      $this->admin = false;
    $this->csrf_token = UtilCSRF::gen('action_members');

    $this->become_admin = !$this->getGuardUser()->isPetitionAdmin($this->petition) && $this->petition->getCampaign()->getBecomePetitionAdmin();
    if ($this->become_admin)
      $this->csrf_token_admin = UtilCSRF::gen('action_join_admin');
  }

}