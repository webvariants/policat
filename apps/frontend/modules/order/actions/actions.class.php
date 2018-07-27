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
 * order actions.
 *
 * @package    policat
 * @subpackage order
 * @author     Martin
 */
class orderActions extends policatActions {

  public function executeNew(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'));
    /* @var $campaign Campaign */
    if (!$campaign) {
      return $this->notFound();
    }

    if (!$this->getGuardUser()->isCampaignMember($campaign)) {
      return $this->noAccess();
    }

    if ($campaign->getOrderId()) {
      return $this->notFound('There is an active order.');
    }

    $make_offer = !!$request->getPostParameter('offer');
    $order = new Order();
    $order->setUser($this->getGuardUser());
    $this->prefillOrder($order, $campaign);
    $form = new OrderNewForm($order, array(
        OrderNewForm::OPTION_CAMPAIGN => $campaign,
        OrderNewForm::OPTION_PRENVENT_SAVE => $make_offer
    ));

    if ($request->isMethod('post')) {
      $form_data = $request->getPostParameter($form->getName());
      if ($form_data) {
        $form->bind($form_data);

        if ($form->isValid()) {
          $form->save();

          if ($make_offer) {
            $offer = OfferTable::getInstance()->offer($order);
            return $this->ajax()
                ->appendPartial('body', 'offer_modal', array('id' => $offer->getId()))
                ->modal('#offer_pdf_modal')
                ->render();
          }

          return $this->ajax()->redirectRotue('order_show', array('id' => $order->getId()))->render();
        } else {
          return $this->ajax()->form($form)->render();
        }
      }
    }

    $this->form = $form;
    $this->campaign = $campaign;
  }

  private function prefillOrder(Order $order, Campaign $campaign) {
    $user = $order->getUser();

    $last_order = OrderTable::getInstance()->fetchLastOrder($campaign, $user);
    if ($last_order) {
      $order->setFirstName($last_order->getFirstName());
      $order->setLastName($last_order->getLastName());
      $order->setOrganisation($last_order->getOrganisation());
      $order->setStreet($last_order->getStreet());
      $order->setCity($last_order->getCity());
      $order->setPostCode($last_order->getPostCode());
      $order->setCountry($last_order->getCountry());
      $order->setVat($last_order->getVat());
    } else {
      $order->setFirstName($user->getFirstName());
      $order->setLastName($user->getLastName());
      $order->setOrganisation($user->getOrganisation());
      $order->setStreet($user->getStreet());
      $order->setCity($user->getCity());
      $order->setPostCode($user->getPostCode());
      $order->setCountry($user->getCountry());
      $order->setVat($user->getVat());
    }
  }

  public function executeShow(sfWebRequest $request) {
    $order = OrderTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $order Order */

    if (!$order) {
      return $this->notFound();
    }

    if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) && $order->getUserId() !== $this->getGuardUser()->getId()) {
      return $this->noAccess();
    }

    $quota = $order->getQuotas()->getFirst();
    /* @var $quota Quota */

    $tax = $order->getTax();
    $price = $tax ? $quota->getPriceBrutto($tax) : $quota->getPrice();

    $markup = trim(StoreTable::value(StoreTable::BILLING_PAYINFO, ''));
    if ($markup) {
      $markup = UtilMarkdown::transform($markup);
      $number = new sfNumberFormat('en');
      $markup = strtr($markup, array(
          '#PRICE#' => $number->format($price, 'c', StoreTable::value(StoreTable::BILLING_CURRENCY)),
          '#ORDER#' => $order->getId()
      ));
    }

    $this->order = $order;
    $this->campaign = $quota->getCampaign();
    $this->markup = $markup;
    $this->paypal = $order->getStatus() == OrderTable::STATUS_ORDER && StoreTable::value(StoreTable::BILLING_PAYPAL_MODE);
    $this->csrf_token = UtilCSRF::gen('new_user_bill', $order->getId());
  }

  public function executeDelete(sfWebRequest $request) {

    $order = OrderTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $order Order */
    if (!$order) {
      return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('delete_order', $order->getId());
    $order_page = (int) $request->getParameter('order_page');

    $deleteable = $order->deleteable();
    $admin = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
    if (!$admin && $this->getUser()->getUserId() != $order->getUserId()) {
      return $this->noAccess();
    }

    $delete = $deleteable || ($admin && $order->getStatus() == OrderTable::STATUS_CANCELATION);

    if (!$admin && !$deleteable) {
      return $this->noAccess();
    }

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#order_delete_modal .modal-body')->render();
      }

      $quotas = $order->getQuotas();
      if ($quotas->count()) {
        $campaign = $quotas->getFirst()->getCampaign();
        /* @var $campaign Campaign */
      } else {
        $campaign = null;
      }

      $con = OrderTable::getInstance()->getConnection();
      try {
        $con->beginTransaction();
        if ($delete) {
          $order->getQuotas()->delete();
          if (!$order->getBill()->isNew()) {
            $order->getBill()->delete();
          }
          $order->delete();
        } else {
          // only for admin
          if ($campaign) {
            if ($campaign->getOrderId() == $order->getId()) {
              $campaign->setOrder(null);
              $campaign->save();
            }
          }

          foreach ($order->getQuotas() as $quota) {
            /* @var $quota Quota */
            $quota->setStatus(QuotaTable::STATUS_CANCELATION);
            $quota->save();
          }
          $order->setStatus(OrderTable::STATUS_CANCELATION);
          $order->save();
        }
        $con->commit();
        if ($order_page) {
          return $this->ajax()->redirectRotue('order_list', array('page' => $order_page))->render();
        } else {
          if ($delete) {
            if ($campaign) {
              return $this->ajax()->redirectRotue('quota_list', array('id' => $campaign->getId()))->render();
            } else {
              return $this->ajax()->redirectRotue('dashboard')->render();
            }
          } else {
            return $this->ajax()->redirectRotue('order_show', array('id' => $order->getId()))->render();
          }
        }
      } catch (\Exception $e) {
        $con->rollback();
        return $this->ajax()->alert('Database problem.', 'Error', '#order_delete_modal .modal-body')->render();
      }
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $order->getId(), 'delete' => $delete, 'csrf_token' => $csrf_token, 'order_page' => $order_page))
        ->modal('#order_delete_modal')
        ->render();
  }

  public function executeList(sfWebRequest $request) {
    $query = QuotaTable::getInstance()->queryAll();
    $this->order_page = (int) $request->getParameter('page', 1);
    $this->quotas = new policatPager($query, $this->order_page, 'order_list_page', array(), false, 20);
  }

  public function executePaid(sfWebRequest $request) {

    $order = OrderTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $order Order */
    if (!$order) {
      return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('paid_order', $order->getId());
    $order_page = (int) $request->getParameter('order_page');

    $quotas = $order->getQuotas();
    if ($quotas->count()) {
      $price = $quotas->getFirst()->getPrice();
    } else {
      $price = null;
    }

    if ($order->getStatus() != OrderTable::STATUS_ORDER) {
      return $this->noAccess();
    }

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#order_paid_modal .modal-body')->render();
      }

      if (OrderTable::getInstance()->paid($order)) {
        return $this->ajax()->redirectRotue('order_list', array('page' => $order_page))->render();
      } else {
        return $this->ajax()->alert('Database problem.', 'Error', '#order_paid_modal .modal-body')->render();
      }
    }

    return $this->ajax()
        ->appendPartial('body', 'paid', array('id' => $order->getId(), 'csrf_token' => $csrf_token, 'order_page' => $order_page, 'price' => $price))
        ->modal('#order_paid_modal')
        ->render();
  }

  public function executeEditBilling(sfWebRequest $request) {
    $campaign = CampaignTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $campaign Campaign */
    if (!$campaign) {
      return $this->ajax()->alert('Campaign not found', 'Error', '#campaign_billing', 'append')->render();
    }

    $form = new CampaignBillingForm($campaign);
    $form->bind($request->getPostParameter($form->getName()));

    if ($form->isValid()) {
      $form->save();
      QuotaTable::getInstance()->activateQuota($campaign, true, 'force');
      return $this->ajax()->render();
    } else {
      return $this->ajax()->alert('Invalid data', 'Error', '#campaign_billing', 'append')->render();
    }
  }

  private function pdfOffer(Offer $offer, $download = false) {
    define('DOMPDF_ENABLE_AUTOLOAD', false);
    define('DOMPDF_ENABLE_CSS_FLOAT', true);
    require_once __DIR__ . '/../../../../../lib/vendor/dompdf/dompdf/dompdf_config.inc.php';

    $dompdf = new DOMPDF();
    $dompdf->load_html($this->getPartial('offer_pdf', array('offer' => $offer)), 'UTF-8');
    $dompdf->render();
    $pdf = $dompdf->output();

    $response = $this->getResponse();
    $response->setContent($pdf);
    if ($response instanceof sfWebResponse) {
      $response->setContentType('application/pdf');
      if ($download) {
        $response->setHttpHeader('Content-Disposition', 'attachment; filename=' . '"offer' . $offer->getId() . '.pdf"');
      }

      return sfView::NONE;
    }
  }

  public function executeOffer(sfWebRequest $request) {
    $offer = OfferTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $offer Offer */
    if (!$offer) {
      return $this->notFound();
    }

    if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) && $offer->getUserId() !== $this->getGuardUser()->getId()) {
      return $this->noAccess();
    }

    return $this->pdfOffer($offer, $request->getParameter('view') === 'download');
  }

  public function executeManualUser(sfWebRequest $request) {
    $quota = QuotaTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $quota Quota */
    if (!$quota || !$quota->getCampaignId()) {
      return $this->notFound();
    }

    $users = sfGuardUserTable::getInstance()->queryByCampaign($quota->getCampaign())->execute();

    return $this->ajax()->appendPartial('body', 'manual_user', array(
        'quota' => $quota,
        'users' => $users
    ))->modal('#order_manual_user')->render();
  }

  public function executeManual(sfWebRequest $request) {
    $quota = QuotaTable::getInstance()->findOneById($request->getParameter('id'));
    $user = sfGuardUserTable::getInstance()->findOneById($request->getParameter('user_id'));
    /* @var $quota Quota */
    if (!$quota || !$user || !$quota->getCampaignId()) {
      return $this->notFound();
    }

    $order = new Order();
    $order->setUser($user);
    $this->prefillOrder($order, $quota->getCampaign());
    $form = new OrderNewForm($order, array(
        OrderNewForm::OPTION_CAMPAIGN => $quota->getCampaign(),
        OrderNewForm::OPTION_MANUAL_QUOTA => $quota
    ));

    if ($request->isMethod('post')) {
      $form_data = $request->getPostParameter($form->getName());
      if ($form_data) {
        $form->bind($form_data);

        if ($form->isValid()) {
          $form->save();

          return $this->ajax()->redirectRotue('order_list')->render();
        } else {
          return $this->ajax()->form($form)->render();
        }
      }
    }

    $this->form = $form;
    $this->quota = $quota;
    $this->user = $user;
    $this->campaign = $quota->getCampaign();
  }

public function executeCancelSubscription(sfWebRequest $request) {

  $quota = QuotaTable::getInstance()->findOneById($request->getParameter('id'));
  /* @var $quota Quota */
  if (!$quota || !$quota->getSubscription()) {
    return $this->notFound();
  }

  $csrf_token = UtilCSRF::gen('cancel_quota', $quota->getId());
  $campaign = $quota->getCampaign();
  if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) && !$this->getGuardUser()->isCampaignAdmin($campaign)) {
    return $this->noAccess();
  }

  if ($request->isMethod('post')) {
    if ($request->getPostParameter('csrf_token') != $csrf_token) {
      return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#cancel_subscription_modal .modal-body')->render();
    }

    $quota->setSubscription(0);
    $quota->save();

    return $this->ajax()->redirectRotue('campaign_edit_', array('id' => $campaign->getId()))->render();
  }

  return $this->ajax()
      ->appendPartial('body', 'cancelSubscription', array('id' => $quota->getId(), 'csrf_token' => $csrf_token, 'campaign' => $campaign))
      ->modal('#cancel_subscription_modal')
      ->render();
}
}
