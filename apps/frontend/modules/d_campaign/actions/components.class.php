<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class d_campaignComponents extends policatComponents {

  public function executeMyCampaigns() {
    $user = $this->getGuardUser();
    if ($user) {
      $query = CampaignTable::getInstance()->queryByMember($user, true, false, false);
      $query->orderBy($query->getRootAlias() . '.name ASC');
      $this->list = $query->execute();
    }
  }

  public function executeMembers() {
    $this->campaign_rights_list = CampaignRightsTable::getInstance()->queryByCampaign($this->campaign)->execute();
    $this->admin = $this->getGuardUser()->isCampaignAdmin($this->campaign);
    $this->csrf_token = UtilCSRF::gen('revoke', $this->campaign->getId());
    if ($this->admin) {
      $this->form = new CampaignAddMemberForm(array(), array('campaign' => $this->campaign));
      $this->invitations = $this->campaign->getInvitationCampaign();
    }
  }

  public function executeEditSwitches() {
    $this->form = new CampaignSwitchesForm($this->campaign);
  }

  public function executeEditPublic() {
    $this->form = new CampaignPublicEnableForm($this->campaign);
  }
  
  public function executeList() {
    $page = isset($this->page) ? $this->page : 1;
    $query = CampaignTable::getInstance()->queryAll(true)->orderBy('name ASC');
    
    $this->campaigns = new policatPager($query, $page, 'campaign_list_pager', array(), true, 20);
  }
  
}
