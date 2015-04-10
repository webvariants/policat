<?php

/**
 * dashboard home actions.
 *
 * @package    policat
 * @subpackage d_home
 * @author     Martin
 */
class d_homeActions extends policatActions {

  public function preExecute() {
    parent::preExecute();

    $response = $this->getResponse();
    if ($response instanceof sfWebResponse) {
      $response->addJavascript('policat_widget_outer_opt', 'last');
      $response->addJavascript('dashboard_home', 'last');
    }
  }

  public function executeIndex(sfWebRequest $request) {
    
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
    $contact_content = $store->findByKeyCached(StoreTable::CONTACT_CONTENT);
    $contact_title = $store->findByKeyCached(StoreTable::CONTACT_TITLE);

    if ($contact_content)
      $this->setContentTags($contact_content);
    if ($contact_title)
      $this->addContentTags($contact_title);

    $this->contact_content = $contact_content ? $contact_content->getValue() : '';
    $this->contact_title = $contact_title ? $contact_title->getValue() : '';
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
  
}