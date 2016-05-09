<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class homeComponents extends policatComponents {

  public function executeFooter() {
    $store = StoreTable::getInstance();
    $terms = $store->findByKeyCached(StoreTable::TERMS_FOOTER);
    $terms_title = $store->findByKeyCached(StoreTable::TERMS_TITLE);
    $contact = $store->findByKeyCached(StoreTable::CONTACT_FOOTER);
    $contact_title = $store->findByKeyCached(StoreTable::CONTACT_TITLE);
    $imprint = $store->findByKeyCached(StoreTable::IMPRINT_FOOTER);
    $imprint_title = $store->findByKeyCached(StoreTable::IMPRINT_TITLE);
    $footer_title = $store->findByKeyCached(StoreTable::FOOTER_TITLE);
    $footer_link = $store->findByKeyCached(StoreTable::FOOTER_LINK);

    if ($terms)
      $this->addContentTags($terms);
    if ($terms_title)
      $this->addContentTags($terms_title);
    if ($contact)
      $this->addContentTags($contact);
    if ($contact_title)
      $this->addContentTags($contact_title);
    if ($imprint)
      $this->addContentTags($imprint);
    if ($imprint_title)
      $this->addContentTags($imprint_title);
    if ($footer_title)
      $this->addContentTags($footer_title);
    if ($footer_link)
      $this->addContentTags($footer_link);

    $this->terms = $terms ? $terms->getValue() : '';
    $this->terms_title = $terms_title ? $terms_title->getValue() : '';
    $this->contact = $contact ? $contact->getValue() : '';
    $this->contact_title = $contact_title ? $contact_title->getValue() : '';
    $this->imprint = $imprint ? $imprint->getValue() : '';
    $this->imprint_title = $imprint_title ? $imprint_title->getValue() : '';
    $this->footer_title = $footer_title ? $footer_title->getValue() : '';
    $this->footer_link = $footer_link ? $footer_link->getValue() : '';
  }

}
