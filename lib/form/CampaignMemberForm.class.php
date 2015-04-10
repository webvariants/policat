<?php

class CampaignMemberForm extends CampaignForm {
  public function configure() {
    parent::configure();

    unset($this['name'], $this['data_owner_id']);
  }
}