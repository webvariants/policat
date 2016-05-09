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
 * quota actions.
 *
 * @package    policat
 * @subpackage quota
 * @author     Martin
 */
class quotaActions extends policatActions {

  public function executeEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();

    if (isset($route_params['type']) && $route_params['type'] == 'new') { // CREATE
      $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'));
      /* @var $campaign Campaign */
      if (!$campaign) {
        return $this->notFound();
      }

      $quota = new Quota();
      $quota->setUser($this->getGuardUser());
      $quota->setCampaign($campaign);
    } else {
      $quota = QuotaTable::getInstance()->findOneById($request->getParameter('id'));
      if (!$quota) {
        return $this->notFound();
      }
    }

    $form = new QuotaForm($quota);

    if ($request->isMethod('post')) {
      $form_data = $request->getPostParameter($form->getName());
      if ($form_data) {
        $form->bind($form_data);

        if ($form->isValid()) {
          $form->save();
          if ($quota->getCampaignId()) {
            return $this->ajax()->redirectRotue('quota_list', array('id' => $quota->getCampaign()->getId()))->render();
          } else {
            return $this->ajax()->redirectRotue('order_list')->render();
          }
        } else {
          return $this->ajax()->form($form)->render();
        }
      }
    }

    $this->form = $form;
    $this->campaign = $quota->getCampaign();
  }

  public function executeList(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign) {
      return $this->notFound();
    }

    if (!$this->getGuardUser()->isCampaignMember($campaign)) {
      return $this->noAccess();
    }
    
    $billingEnabled = StoreTable::value(StoreTable::BILLING_ENABLE);
    
    $order = $campaign->getOrderId() ? $campaign->getOrder() : null;
    $this->order = $order;
    $this->orderEdit = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) || ($order && $order->getUserId() == $this->getGuardUser()->getId());
    
    // recheck quota
    QuotaTable::getInstance()->activateQuota($campaign);

    $this->campaign = $campaign;
    $this->billingEnabled = $billingEnabled;

  }

}
