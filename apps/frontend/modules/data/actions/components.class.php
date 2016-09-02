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
    $this->download_url = null;
    $this->action = '';

    $this->route = null;
    $this->route_params = array();

    $data_owner_id = null;

    $download_route = null;
    $download_route_id = null;

    if (isset($this->campaign)) {
      // check the rights for subscriptions
      if ($this->subscriptions && !$this->getGuardUser()->isDataOwnerOfCampaign($this->campaign)) {
        $this->subscriptions = false;
      }

      $query = PetitionSigningTable::getInstance()->query(array(
          PetitionSigningTable::CAMPAIGN => $this->campaign,
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
          PetitionSigningTable::SUBSCRIBER => $this->subscriptions
      ));
      $this->form = new SigningsDownloadForm(array(), array(
          PetitionSigningTable::CAMPAIGN => $this->campaign,
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
          SigningsDownloadForm::OPTION_QUERY => $query->copy(),
          SigningsDownloadForm::OPTION_SUBSCRIBER => $this->subscriptions
      ));
      $this->form->bindSelf('c' . $this->campaign->getId());

      $this->route = 'data_campaign_pager';
      $this->route_params = array('id' => $this->campaign->getId());
      $this->show_petition = true;
      $data_owner_id = $this->campaign->getDataOwnerId();
      $download_route = 'data_campaign_download';
      $download_route_id = $this->campaign->getId();
    } elseif (isset($this->petition)) {
      // check the rights for subscriptions
      if ($this->subscriptions && !$this->getGuardUser()->isDataOwnerOfCampaign($this->petition->getCampaign())) {
        $this->subscriptions = false;
      }

      $query = PetitionSigningTable::getInstance()->query(array(
          PetitionSigningTable::PETITION => $this->petition,
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
          PetitionSigningTable::SUBSCRIBER => $this->subscriptions
      ));
      $this->form = new SigningsDownloadForm(array(), array(
          PetitionSigningTable::PETITION => $this->petition,
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
          SigningsDownloadForm::OPTION_QUERY => $query->copy(),
          SigningsDownloadForm::OPTION_SUBSCRIBER => $this->subscriptions
      ));
      $this->form->bindSelf('p' . $this->petition->getId());

      $this->route = 'data_petition_pager';
      $this->route_params = array('id' => $this->petition->getId());
      $data_owner_id = $this->petition->getCampaign()->getDataOwnerId();
      $download_route = 'data_petition_download';
      $download_route_id = $this->petition->getId();
    } elseif (isset($this->widget)) { // this is for widget owners only
      $query = PetitionSigningTable::getInstance()->query(array(
          PetitionSigningTable::WIDGET => $this->widget,
          PetitionSigningTable::USER => $this->getGuardUser(),
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
          PetitionSigningTable::SUBSCRIBER => $this->subscriptions
      ));
      $this->form = new SigningsDownloadForm(array(), array(
          PetitionSigningTable::WIDGET => $this->widget,
          PetitionSigningTable::USER => $this->getGuardUser(),
          PetitionSigningTable::ORDER => PetitionSigningTable::ORDER_DESC,
          SigningsDownloadForm::OPTION_QUERY => $query->copy(),
          SigningsDownloadForm::OPTION_SUBSCRIBER => $this->subscriptions
      ));
      $this->form->bindSelf('w' . $this->widget->getId());

      $this->route = 'data_widget_pager';
      $this->route_params = array('id' => $this->widget->getId());
      $data_owner_id = $this->widget->getCampaign()->getDataOwnerId();
      $download_route = 'data_widget_download';
      $download_route_id = $this->widget->getId();
    }

    $this->can_delete = $this->getUser()->getUserId() == $data_owner_id;

    $this->signings = new policatPager($query, $page, $this->route, $this->route_params, true, 20, $this->form, null, array('s' => $this->subscriptions ? 1 : 0));

    if ($this->form->isValid()) {
      $this->count = $this->signings->getNbResults();
      $this->pending = $this->form->getPending();
      $this->show_subscriber = !$this->subscriptions;
      $this->show_email = $this->subscriptions;

      $download_params = array(
          's' => $this->subscriptions ? 1 : 0
      );
      $download_params[$this->form->getName()] = $this->form->getValues();

      $this->download_url = $this->getContext()->getRouting()->generate($download_route, array(
            'id' => $download_route_id
        )) . '?' . http_build_query($download_params, null, '&');

      if (isset($this->petition) && $download_route === 'data_petition_download') {
        $this->download_incremental_url = $this->getContext()->getRouting()->generate($download_route, array(
              'id' => $this->petition->getId()
          )) . '?' . http_build_query(array(
              's' => $this->subscriptions ? 1 : 0,
              'incremental' => 1
          ), null, '&');
      }
    }
  }

}
