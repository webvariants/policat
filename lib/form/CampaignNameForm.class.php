<?php

class CampaignNameForm extends CampaignForm {
  public function configure() {
    parent::configure();

    unset($this['sf_guard_user_list'], $this['data_owner_id']);
  }
}