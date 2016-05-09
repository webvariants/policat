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
 * bill actions.
 *
 * @package    policat
 * @subpackage bill
 * @author     Martin
 */
class billActions extends policatActions {

  public function executeNew(sfWebRequest $request) {
    $order = OrderTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $order Order */
    if (!$order) {
      return $this->notFound();
    }

    $bill = $order->getBill();
    /* @var $bill Bill */
    if (!$bill->isNew()) {
      return $this->ajax()->alert('Bill already exists.', 'Error')->render();
    }

    $csrf_token = UtilCSRF::gen('new_bill', $order->getId());
    $order_page = (int) $request->getParameter('order_page');

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#bill_new_modal .modal-body')->render();
      }

      if (BillTable::getInstance()->bill($order)) {
        return $this->ajax()->redirectRotue('order_list', array('page' => $order_page))->render();
      } else {
        return $this->ajax()->alert('Database problem.', 'Error', '#bill_new_modal .modal-body')->render();
      }
    }

    return $this->ajax()
        ->appendPartial('body', 'new', array('id' => $order->getId(), 'csrf_token' => $csrf_token, 'order_page' => $order_page))
        ->modal('#bill_new_modal')
        ->render();
  }

  private function pdf(Bill $bill, $download = false, $return_pdf = false) {
    define('DOMPDF_ENABLE_AUTOLOAD', false);
    define('DOMPDF_ENABLE_CSS_FLOAT', true);
    require_once __DIR__ . '/../../../../../lib/vendor/dompdf/dompdf/dompdf_config.inc.php';

    $dompdf = new DOMPDF();
    $dompdf->load_html($this->getPartial('bill', array('bill' => $bill)), 'UTF-8');
    $dompdf->render();
    $pdf = $dompdf->output();
    
    if ($return_pdf) {
      return $pdf;
    }

    $response = $this->getResponse();
    $response->setContent($pdf);
    if ($response instanceof sfWebResponse) {
      $response->setContentType('application/pdf');
      if ($download) {
        $response->setHttpHeader('Content-Disposition', 'attachment; filename=' . '"invoice_' . $bill->getId() . '.pdf"');
      }

      return sfView::NONE;
    }
  }

  public function executeShow(sfWebRequest $request) {
    $bill = BillTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $bill Bill */
    if (!$bill) {
      return $this->notFound();
    }

    $view = (string) $request->getParameter('view');
    $download = $view === 'download';
    $popup = $view === 'popup';

    if ($popup) {
      return $this->ajax()
          ->appendPartial('body', 'billpopup', array('id' => $bill->getId(), 'bill' => $bill))
          ->modal('#bill_pdf_modal')
          ->render();
    }

    return $this->pdf($bill, $download);
  }

  public function executeUser(sfWebRequest $request) {
    $order = OrderTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $order Order */

    if (!$order) {
      return $this->notFound();
    }

    if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) && $order->getUserId() !== $this->getGuardUser()->getId()) {
      return $this->noAccess();
    }

    $bill = $order->getBill();
    /* @var $bill Bill */
    if ($bill->isNew()) {
      $csrf_token = UtilCSRF::gen('new_user_bill', $order->getId());

      if ($request->getPostParameter('csrf_token') != $csrf_token) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error')->render();
      }

      if ($order->getStatus() == OrderTable::STATUS_CANCELATION) {
        return $this->ajax()->alert('You can not create an invoice for a canceled order.', 'Error')->render();
      }

      if (!BillTable::getInstance()->bill($order)) {
        return $this->ajax()->alert('Database problem.', 'Error')->render();
      }
    }

    if ($bill->isNew()) {
      return $this->notFound();
    }

    if ($request->isMethod('post')) {
      return $this->ajax()
          ->appendPartial('body', 'billpopupuser', array('id' => $order->getId(), 'bill' => $bill))
          ->modal('#bill_pdf_modal')
          ->render();
    }

    return $this->pdf($order->getBill(), $request->getParameter('view') === 'download');
  }

  public function executeMail(sfWebRequest $request) {
    $this->ajax()->setAlertTarget('#bill_pdf_modal .modal-header');
    $bill = BillTable::getInstance()->findOneById($request->getParameter('id'));
    /* @var $bill Bill */
    if (!$bill) {
      return $this->notFound();
    }

    if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) && $bill->getUserId() !== $this->getGuardUser()->getId()) {
      return $this->noAccess();
    }

    /* @var $bill Bill */

    $subject = strtr(StoreTable::value(StoreTable::BILLING_BILL_MAIL_SUBJECT), $bill->getSubst1());
    $body = strtr(UtilMarkdown::transform(strtr(StoreTable::value(StoreTable::BILLING_BILL_MAIL_BODY), $bill->getSubst1())), $bill->getSubst2());
    $user = $bill->getUser();
    
    $attachments = array(new Swift_Attachment($this->pdf($bill, false, true), 'invoice_' . $bill->getId() . '.pdf', 'application/pdf'));
    
    UtilMail::send(null, null, $user->getSwiftEmail(), $subject, $body, 'text/html', null, null, null, $attachments);
    
    return $this->ajax()->alert('Mail sent', '')->render();
  }

}
