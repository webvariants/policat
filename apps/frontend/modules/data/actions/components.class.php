<?php

/**
 * @property Campaign $campaign
 * @property Petition $petition
 * @property Widget $widget
 */
class dataComponents extends policatComponents {

  function executeList() {
    $page = isset($this->page) ? $this->page : 1;
    $this->show_petition = false;
    $this->show_subscriber = true;
    $this->show_status = false;
    $this->show_email = false;
    $this->download_filter = null;
    $this->action = '';

    $this->route = null;
    $this->route_params = array();

    $data_owner_id = null;

    if (isset($this->campaign)) {
      $query = PetitionSigningTable::getInstance()->query(array(
          PetitionSigningTable::CAMPAIGN => $this->campaign,
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
        ));
      $this->form = new SigningsDownloadForm(array(), array(
          PetitionSigningTable::CAMPAIGN => $this->campaign,
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
            SigningsDownloadForm::OPTION_QUERY => $query->copy(),
          SigningsDownloadForm::OPTION_IS_DATA_OWNER => $this->getGuardUser()->isDataOwnerOfCampaign($this->campaign)
        ));
      $this->form->bindSelf('c' . $this->campaign->getId());

      $this->route = 'data_campaign_pager';
      $this->route_params = array('id' => $this->campaign->getId());
      $this->show_petition = true;
      $data_owner_id = $this->campaign->getDataOwnerId();
    } elseif (isset($this->petition)) {

      $query = PetitionSigningTable::getInstance()->query(array(
          PetitionSigningTable::PETITION => $this->petition,
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC
        ));
      $this->form = new SigningsDownloadForm(array(), array(
            PetitionSigningTable::PETITION => $this->petition,
            PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
            SigningsDownloadForm::OPTION_QUERY => $query->copy(),
            SigningsDownloadForm::OPTION_IS_DATA_OWNER => $this->getGuardUser()->isDataOwnerOfCampaign($this->petition->getCampaign())
        ));
      $this->form->bindSelf('p' . $this->petition->getId());

      $this->route = 'data_petition_pager';
      $this->route_params = array('id' => $this->petition->getId());
      $data_owner_id = $this->petition->getCampaign()->getDataOwnerId();
    } elseif (isset($this->widget)) { // this is for widget owners only
      $query = PetitionSigningTable::getInstance()->query(array(
          PetitionSigningTable::WIDGET => $this->widget,
          PetitionSigningTable::USER => $this->getGuardUser(),
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC
        ));
      $this->form = new SigningsDownloadForm(array(), array(
            PetitionSigningTable::WIDGET => $this->widget,
            PetitionSigningTable::USER => $this->getGuardUser(),
            PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
            SigningsDownloadForm::OPTION_QUERY => $query->copy(),
            SigningsDownloadForm::OPTION_IS_DATA_OWNER => true
        ));
      $this->form->bindSelf('w' . $this->widget->getId());

      $this->route = 'data_widget_pager';
      $this->route_params = array('id' => $this->widget->getId());
      $data_owner_id = $this->widget->getCampaign()->getDataOwnerId();
    }

    $this->can_delete = $this->getUser()->getUserId() == $data_owner_id;
    $this->signings = new policatPager($query, $page, $this->route, $this->route_params, true, 20, $this->form);

    if ($this->form->isValid()) {
      $this->count = $this->form->getCount();
      $this->pages = UtilExport::pages($this->count);
      $this->pending = $this->form->getPending();
      $this->download_filter = array($this->form->getName() => $this->form->getValues());
      $this->show_subscriber = !$this->form->getSubscriber();
      $this->show_email = $this->form->getSubscriber();
    }
  }

}