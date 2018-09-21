<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/** @property $campaign Campaign */
class orderComponents extends policatComponents {

  public function executeSidebar() {
    $order = $this->campaign->getOrderId() ? $this->campaign->getOrder() : null;
    $this->order = $order;
    $this->orderEdit = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) || ($order && $order->getUserId() == $this->getGuardUser()->getId());

    $quota = $this->campaign->getQuotaId() ? $this->campaign->getQuota() : null;
    $this->quota = $quota;
    $this->show = $this->campaign->getBillingEnabled() || $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
    $this->admin = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) || $this->getGuardUser()->isCampaignAdmin($this->campaign);
  }

  public function executeEditBilling() {
    if ($this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN)) {
      $this->form = new CampaignBillingForm($this->campaign);
    } else {
      $this->form = false;
      $this->enabled = $this->campaign->getBillingEnabled();
    }
  }

  public function executeNotice() {
    $this->showNotice = $this->campaign->getBillingEnabled() && !$this->campaign->getQuotaId();
    $this->showBuy = !$this->campaign->getOrderId();
    $this->showOrder = $this->campaign->getOrderId() && $this->campaign->getOrder()->getUserId() == $this->getGuardUser()->getId();
  }

}
