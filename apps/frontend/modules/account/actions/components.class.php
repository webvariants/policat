<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class accountComponents extends policatComponents {

  public function executeMembership() {
    if (!$this->user->isNew()) {
      $this->campaign_rights_list = CampaignRightsTable::getInstance()->queryByUser($this->user)->execute();
      $this->petition_rights_list = PetitionRightsTable::getInstance()->queryByUser($this->user)->execute();

      if (isset($this->join)) {
        $this->join_form = new SelectCampaignForm(array(), array(
            SelectCampaignForm::JOINABLE => true,
            SelectCampaignForm::USER => $this->user,
            SelectCampaignForm::IS_MEMBER => false,
            SelectCampaignForm::EMPTY_STR => 'join campaign',
            SelectCampaignForm::NAME => 'select_join_campaign',
            SelectCampaignForm::HELP => 'Join the campaign of your group or organisation. Within each campaign, you can start as many actions as you like - simultaneously or consecutively.'
        ));
      }
    }
  }

}
