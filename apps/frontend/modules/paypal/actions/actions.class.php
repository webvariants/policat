<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;

/**
 * paypal actions.
 *
 * @package    policat
 * @subpackage paypal
 * @author     Martin
 */
class paypalActions extends policatActions {

  private $api_context = null;

  private function buildPayment(Order $order) {
    $bill = $order->getBill();
    if ($bill->isNew()) {
      if (!BillTable::getInstance()->bill($order)) {
        return null;
      }
    }
    
    $payer = new Payer();
    $payer->setPaymentMethod("paypal");

    $items = array();
    $total = 0.0;
    $tax = 0.0;

    foreach ($order->getQuotas() as $quota) {
      /* @var $quota Quota */

      $item = new Item();
      $item->setName(StoreTable::value(StoreTable::BILLING_PAYPAL_PRODUCT_PREFIX, '') . $quota->getName())
        ->setCurrency(StoreTable::value(StoreTable::BILLING_CURRENCY))
        ->setQuantity(1)
        ->setPrice($quota->getPrice())
        ->setTax($quota->getPriceTax($order->getTax()))
        ->setDescription($quota->getEmails() . ' e-mails / participants for ' . $quota->getDays() . ' days');
      ;

      $items[] = $item;
      $total += $quota->getPrice();
      $tax += $quota->getPriceTax($order->getTax());
    }

    $details = new Details();
    $details
      ->setSubtotal($total)
      ->setTax($tax);

    $itemList = new ItemList();
    $itemList->setItems($items);

    $amount = new Amount();
    $amount->setCurrency(StoreTable::value(StoreTable::BILLING_CURRENCY))
      ->setTotal($total + $tax)
      ->setDetails($details);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($itemList)
      ->setDescription(StoreTable::value(StoreTable::BILLING_PAYPAL_TRANSACTION_DESCRIPTION))
      ->setInvoiceNumber($bill->getId());

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($this->getContext()->getRouting()->generate('paypal_pay_return', array('id' => $order->getId()), true))
      ->setCancelUrl($this->getContext()->getRouting()->generate('paypal_pay_cancel', array('id' => $order->getId()), true));

    $payment = new Payment();
    $payment->setIntent("sale")
      ->setPayer($payer)
      ->setRedirectUrls($redirectUrls)
      ->setTransactions(array($transaction));

    try {
      $payment->create($this->getApiContext());
    } catch (Exception $ex) {

      return null;
    }

    return $payment;
  }

  /**
   * @return ApiContext
   */
  private function getApiContext() {
    if ($this->api_context === null) {
      $mode = StoreTable::value(StoreTable::BILLING_PAYPAL_MODE);
      $client_id = StoreTable::value(StoreTable::BILLING_PAYPAL_CLIENT_ID);
      $secret = StoreTable::value(StoreTable::BILLING_PAYPAL_SECRET);

      if ($mode && $client_id && $secret) {
        $sdkConfig = array(
            'mode' => $mode
        );

        $credential = new OAuthTokenCredential($client_id, $secret);
        $this->api_context = new ApiContext($credential);
        $this->api_context->setConfig($sdkConfig);
      }
    }

    return $this->api_context;
  }

  public function executePay(sfWebRequest $request) {
    $api_context = $this->getApiContext();

    if (!$api_context) {
      return $this->ajax()->alert('Bad configuration.', 'Paypal')->render();
    }

    $order = OrderTable::getInstance()->findOneById($request->getParameter('id'));
    if (!$order) {
      return $this->notFound();
    }

    /* @var $order Order */

    if ($order->getUserId() != $this->getUser()->getUserId()) {
      return $this->noAccess();
    }

    if ($order->getStatus() != OrderTable::STATUS_ORDER) {
      return $this->ajax()->alert('Order in wrong state.', 'Error')->render();
    }

    $payment = null;

    if ($order->getPaypalPaymentId()) {
      $existing_payment = Payment::get($order->getPaypalPaymentId(), $this->getApiContext());
      if (!$existing_payment) {
        return $this->returnPage('Error', 'Payment not found.');
      }

      switch ($existing_payment->getState()) {
        case 'created':
          $payment = $existing_payment;
          break;
        case 'approved':
//          $transactions = $existing_payment->getTransactions();
//          $relatedResources = $transactions[0]->getRelatedResources();
//          $sale = $relatedResources[0]->getSale();
//          die($sale->getState());
          return $this->ajax()->alert('Status: approved', 'Paypal')->render();
        case 'failed':
          return $this->ajax()->alert('Status: failed', 'Paypal')->render();
        case 'canceled':
          return $this->ajax()->alert('Status: cancel', 'Paypal')->render();
        case 'expired':
          return $this->ajax()->alert('Status: expired (The payment authorization has expired and could not be captured.)', 'Paypal')->render();
        case 'pending':
          return $this->ajax()->alert('Status: pending (means that PyPal has to perform some security checks or that the payment is a bank transfer. In the latter PayPal must wait until the bank transfer was completed.)', 'Paypal')->render();
        case 'in_progress':
          return $this->ajax()->alert('Status: in_progress', 'Paypal')->render();
        default:
          return $this->ajax()->alert('unknown status', 'Paypal')->render();
      }
    }

    if (!$payment) {
      $payment = $this->buildPayment($order);
      if ($payment === null) {
        return $this->ajax()->alert('Paypal error', 'Error')->render();
      }

      $order->setPaypalPaymentId($payment->getId());
      $order->save();
    }

    $approvalUrl = $payment->getApprovalLink();

    return $this->ajax()->redirect($approvalUrl)->render();
  }

  private function returnPage($title, $message = null, $order = null) {
    $this->title = $title;
    $this->message = $message;
    if ($order) {
      $this->order = $order;
      /* @var $order Order */
      foreach ($order->getQuotas() as $quota) {
        /* @var $quota Quota */
        if ($quota->getCampaignId()) {
          $this->campaign = $quota->getCampaign();
          break;
        }
      }
    }

    $this->setTemplate('page');
  }

  public function executeReturn(sfWebRequest $request) {
    $order = OrderTable::getInstance()->findOneById($request->getParameter('id'));
    if (!$order) {
      return $this->returnPage('Error', 'Order not found.');
    }

    if ($order->getStatus() == OrderTable::STATUS_PAID) {
      return $this->returnPage('Error', 'Order already paid.', $order);
    }

    if ($order->getStatus() != OrderTable::STATUS_ORDER) {
      return $this->returnPage('Error', 'Order has wrong status (not ordering).', $order);
    }

    /* @var $order Order */
    
    if ($order->getPaypalStatus() == OrderTable::PAYPAL_STATUS_PAYMENT_EXECUTED) {
      return $this->returnPage('Error', 'Paypal Payment already executed.', $order);
    }

    $paymentId = $request->getGetParameter('paymentId');
    $payment = Payment::get($paymentId, $this->getApiContext());
    if (!$payment) {
      return $this->returnPage('Error', 'Payment not found.');
    }

    if ($order->getPaypalPaymentId() != $paymentId) {
      return $this->returnPage('Error', 'Payment not matching order.', $order);
    }

    $execution = new PaymentExecution();
    $execution->setPayerId($request->getGetParameter('PayerID'));

    try {
      $payment->execute($execution, $this->getApiContext());

//      echo '<pre>'; print_r($payment); die;
      $transactions = $payment->getTransactions();
      $relatedResources = $transactions[0]->getRelatedResources();
      $sale = $relatedResources[0]->getSale();
      $order->setPaypalSaleId($sale->getId());
      $order->setPaypalStatus(OrderTable::PAYPAL_STATUS_PAYMENT_EXECUTED);
      $order->save();

      if (OrderTable::getInstance()->paid($order)) {
        return $this->returnPage('Success', 'Order #' . $order->getId() . ' paid.', $order);
      } else {
        return $this->returnPage('Error', 'Database Problem. Please contact us.', $order);
      }
    } catch (Exception $ex) {
      return $this->returnPage('Error', 'Can not execute payment.', $order);
    }
  }

  public function executeCancel(sfWebRequest $request) {
    $order = OrderTable::getInstance()->findOneById($request->getParameter('id'));
    if (!$order) {
      return $this->returnPage('Error', 'Order not found.');
    }

    if ($order->getStatus() == OrderTable::STATUS_PAID) {
      return $this->returnPage('Error', 'Order already paid.', $order);
    }

    /* @var $order Order */

    return $this->returnPage('Cancel', 'Payment with paypal canceled.', $order);
  }

}
