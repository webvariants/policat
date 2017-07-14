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
 * dashboard home actions.
 *
 * @package    policat
 * @subpackage d_home
 * @author     Martin
 */
class d_homeActions extends policatActions {

//  public function preExecute() {
//    parent::preExecute();
//
//    $response = $this->getResponse();
//    if ($response instanceof sfWebResponse) {
//      $response->addJavascript('dist/policat_widget_outer', 'last');
//      $response->addJavascript('dashboard_home', 'last');
//    }
//  }

  public function executeIndex(sfWebRequest $request) {
    $response = $this->getResponse();
    if ($response instanceof sfWebResponse) {
      $response->addJavascript('dist/policat_widget_outer', 'last');
    }
  }

  public function getKeyvisualUrl($params) {
    $petition = PetitionTable::getInstance()->findById($params[1]);
    if ($petition) {
      $keyvisual = $petition->getKeyVisual();
      if ($keyvisual) {
        return sfContext::getInstance()->getRequest()->getRelativeUrlRoot() . '/images/keyvisual/' . $keyvisual;
      }
    }
    return '';
  }

  public function executeIndexB4(sfWebRequest $request) {
    $store = StoreTable::getInstance();

    $this->markup = null;
    $markup = $store->findByKeyCached(StoreTable::PORTAL_HOME_MARKUP_B4);
    if ($markup) {
      $this->setContentTags($markup);

      $markup = preg_replace_callback('/#KEYVISUAL-(\d+)#/', array($this, 'getKeyvisualUrl'), $markup->getValue());
      $markup = preg_replace('/#WIDGET-(\d+)#/', 'PasjhkX\\1KmsownedS', $markup); // prevent markdown messing up widget
      $markup = UtilMarkdown::transform($markup, true, true);
      $this->markup = preg_replace_callback('/PasjhkX(\d+)KmsownedS/', array('UtilWidget', 'renderWidget'), $markup);
    }

    $openActions = UtilOpenActions::dataByCache();
    $joined = array();
    $petition_ids = array();
    foreach ($openActions['open'] as $tab) {
      foreach ($tab['excerpts'] as $action) {
        if (!in_array($action['petition_id'], $petition_ids)) {
          $joined[] = $action;
          $petition_ids[] = $action['petition_id'];
        }
      }
    }

    array_splice($joined, count($joined) - count($joined) % 3);
    $this->actionListChunk = array_chunk($joined, 3);
    $this->styles = $openActions['styles'];
  }

  public function executeTips(sfWebRequest $request) {
    $this->page(StoreTable::TIPS_TITLE, StoreTable::TIPS_CONTENT);
  }

  public function executeFaq(sfWebRequest $request) {
    $this->page(StoreTable::FAQ_TITLE, StoreTable::FAQ_CONTENT);
  }

  public function executeHelp(sfWebRequest $request) {
    $this->page(StoreTable::HELP_TITLE, StoreTable::HELP_CONTENT);
  }

  protected function page($title, $content) {
    $store = StoreTable::getInstance();
    $page_content = $store->findByKeyCached($content);
    $page_title = $store->findByKeyCached($title);

    if ($page_content)
      $this->setContentTags($page_content);
    if ($page_title)
      $this->addContentTags($page_title);

    $this->page_content = $page_content ? $page_content->getValue() : '';
    $this->page_title = $page_title ? $page_title->getValue() : '';
    $this->setTemplate('page');

    if (!$page_content || !$page_content->getValue()) {
      $this->forward404();
    }
  }

  public function executeTerms(sfWebRequest $request) {
    $store = StoreTable::getInstance();
    $terms_content = $store->findByKeyCached(StoreTable::TERMS_CONTENT);
    $terms_title = $store->findByKeyCached(StoreTable::TERMS_TITLE);

    if ($terms_content)
      $this->setContentTags($terms_content);
    if ($terms_title)
      $this->addContentTags($terms_title);

    $this->terms_content = $terms_content ? $terms_content->getValue() : '';
    $this->terms_title = $terms_title ? $terms_title->getValue() : '';
  }

  public function executeContact(sfWebRequest $request) {
    $store = StoreTable::getInstance();
    $contact_user = $store->findByKeyCached(StoreTable::CONTACT_USER);
    if ($contact_user) {
      $this->setContentTags($contact_user);
    }

    $form = ($contact_user && $contact_user->getValue()) ? new ContactTicketForm(array(), array(
        ContactTicketForm::OPTION_USER_ID => $contact_user->getValue()
      )) : null;
    if ($form && $request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));
      if ($form->isValid()) {
        $form->save();
        return $this->ajax()
            ->form($form)
            ->attr('#contactticket input, #contactticket button, #contactticket select, #contactticket textarea', 'disabled', 'disabled')
            ->alert('Thank you', '', '#contactticket .form-actions', 'after')
            ->render();
      } else {
        return $this->ajax()->form($form)->render();
      }
    }

    $contact_content = $store->findByKeyCached(StoreTable::CONTACT_CONTENT);
    $contact_title = $store->findByKeyCached(StoreTable::CONTACT_TITLE);

    if ($contact_content)
      $this->setContentTags($contact_content);
    if ($contact_title)
      $this->addContentTags($contact_title);

    $this->contact_content = $contact_content ? $contact_content->getValue() : '';
    $this->contact_title = $contact_title ? $contact_title->getValue() : '';
    $this->form = $form;
  }

  public function executeImprint(sfWebRequest $request) {
    $store = StoreTable::getInstance();
    $imprint_content = $store->findByKeyCached(StoreTable::IMPRINT_CONTENT);
    $imprint_title = $store->findByKeyCached(StoreTable::IMPRINT_TITLE);

    if ($imprint_content)
      $this->setContentTags($imprint_content);
    if ($imprint_title)
      $this->addContentTags($imprint_title);

    $this->imprint_content = $imprint_content ? $imprint_content->getValue() : '';
    $this->imprint_title = $imprint_title ? $imprint_title->getValue() : '';
  }

  public function executePricing(sfWebRequest $request) {
    $page_content = StoreTable::getInstance()->findByKeyCached(StoreTable::BILLING_PRICING);
    $markdown = '';

    if ($page_content) {
      $this->setContentTags($page_content);
      $markdown = $page_content->getValue();
    }

    $products = ProductTable::getInstance()->queryAll()->execute();
    $number = new sfNumberFormat('en');

    $table = '<table class="table table-bordered" style="width:auto"><tr><th>Package</th><th>E-mails / participants</th><th>Days</th><th>Net</th><th>Gross</th></tr>';

    foreach ($products as $product) {
      /* @var $product Product */
      $table .= sprintf('<tr><td>%s</td><td style="text-align: right;">%s</td><td style="text-align: right;">%s</td><td style="text-align: right;">%s</td><td style="text-align: right;">%s</td></tr>', Util::enc($product->getName()), $number->format($product->getEmails()), $number->format($product->getDays()), $number->format($product->getPrice(), 'c', StoreTable::value(StoreTable::BILLING_CURRENCY)), $number->format($product->getPriceBrutto(), 'c', StoreTable::value(StoreTable::BILLING_CURRENCY)));
    }

    $table .= '</table>';

    $markup = strtr(
      UtilMarkdown::transform(strtr($markdown, array('#PRODUCTS#' => 'a324ehksdf3457dfjgdkhi534wnhksdxfda')), true, true), array(
        'a324ehksdf3457dfjgdkhi534wnhksdxfda' => $table
    ));

    $this->markup = $markup;
  }

}
