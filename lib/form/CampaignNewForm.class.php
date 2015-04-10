<?php

class CampaignNewForm extends CampaignForm {
  public function configure() {
    parent::configure();

    unset($this['data_owner_id']);
    
  }
}