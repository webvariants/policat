<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
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
      if ($this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) || StoreTable::value(StoreTable::CAMAPIGN_CREATE_ON)) {
        $this->create_form = new NewCampaignNameForm();
      }
      $this->join_form = new SelectCampaignForm(array(), array(
          'user' => $user,
          'is_member' => false,
          'empty' => 'join campaign',
          SelectCampaignForm::NAME => 'select_join_campaign',
          SelectCampaignForm::HELP => 'Join the campaign of your group or organisation. Within each campaign, you can start as many actions as you like - simultaneously or consecutively.'
      ));

      $this->edit_form = new SelectCampaignForm(array(), array(
          'user' => $user,
          'is_member' => true,
          'empty' => 'go to campaign',
          SelectCampaignForm::NAME => 'select_edit_campaign'
      ));
    }
  }

  public function executeMembers() {
    $this->campaign_rights_list = CampaignRightsTable::getInstance()->queryByCampaign($this->campaign)->execute();
    $this->admin = $this->getGuardUser()->isCampaignAdmin($this->campaign);
    $this->csrf_token = UtilCSRF::gen('revoke', $this->campaign->getId());
  }

  public function executeEditSwitches() {
    $this->form = new CampaignSwitchesForm($this->campaign);
  }

}
