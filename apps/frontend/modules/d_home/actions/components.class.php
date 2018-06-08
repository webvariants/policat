<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class d_homeComponents extends policatComponents {

  public function executeOpen_actions() {
    $this->data = UtilOpenActions::dataByCache();
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

  public function executeFeature() {
    $title = trim(StoreTable::value(StoreTable::PORTAL_HOME_TITLE, ''));
    $this->title = $title ? $title : 'Featured action';

    $markup = trim(StoreTable::value(StoreTable::PORTAL_HOME_MARKUP, ''));
    if ($markup) {
      $markup = preg_replace_callback('/#KEYVISUAL-(\d+)#/', array($this, 'getKeyvisualUrl'), $markup);
      $markup = preg_replace('/#WIDGET-(\d+)#/', 'PasjhkX\\1KmsownedS', $markup); // prevent markdown messing up widget
      $markup = UtilMarkdown::transform($markup, false);
      $this->markup = preg_replace_callback('/PasjhkX(\d+)KmsownedS/', array('UtilWidget', 'renderWidget'), $markup);
      return;
    }

    $data = UtilOpenActions::dataByCache();
    if ($data && array_key_exists(UtilOpenActions::HOTTEST, $data['open'])) {
      $recent = $data['open'][UtilOpenActions::HOTTEST];
      if ($recent && $recent['excerpts']) {
        $excerpts = $recent['excerpts'];
        if ($excerpts) {
          $this->widget_id = $excerpts[0]['widget_id'];
          $this->stylings = $data['styles'][$this->widget_id];
          $this->stylings['type'] = 'embed';
          $this->stylings['width'] = 'auto';
          $this->stylings['url'] = $this->generateUrl('sign_hp', array('id' => $this->widget_id, 'hash' => $excerpts[0]['widget_last_hash']), true);
        }
      }
    }
  }

  public function executeMenu() {
    $store = StoreTable::getInstance();
    $tips = $store->findByKeyCached(StoreTable::TIPS_MENU);
    $tips_title = $store->findByKeyCached(StoreTable::TIPS_TITLE);
    $faq = $store->findByKeyCached(StoreTable::FAQ_MENU);
    $faq_title = $store->findByKeyCached(StoreTable::FAQ_TITLE);
    $help = $store->findByKeyCached(StoreTable::HELP_MENU);
    $help_title = $store->findByKeyCached(StoreTable::HELP_TITLE);
    $privacy = $store->findByKeyCached(StoreTable::PRIVACY_MENU);
    $privacy_title = $store->findByKeyCached(StoreTable::PRIVACY_TITLE);

    $menu_home = $store->findByKeyCached(StoreTable::MENU_HOME);
    $menu_start = $store->findByKeyCached(StoreTable::MENU_START);
    $menu_join = $store->findByKeyCached(StoreTable::MENU_JOIN);
    $menu_login = $store->findByKeyCached(StoreTable::MENU_LOGIN);

    $pricing = $store->findByKeyCached(StoreTable::BILLING_PRICING_MENU);

    $register = $store->findByKeyCached(StoreTable::REGISTER_ON);

    if ($tips)
      $this->addContentTags($tips);
    if ($tips_title)
      $this->addContentTags($tips_title);

    if ($faq)
      $this->addContentTags($faq);
    if ($faq_title)
      $this->addContentTags($faq_title);

    if ($help)
      $this->addContentTags($help);
    if ($help_title)
      $this->addContentTags($help_title);

    if ($privacy)
      $this->addContentTags($privacy);
    if ($privacy_title)
      $this->addContentTags($privacy_title);

    if ($menu_home)
      $this->addContentTags($menu_home);
    if ($menu_start)
      $this->addContentTags($menu_start);
    if ($menu_join)
      $this->addContentTags($menu_join);
    if ($menu_login)
      $this->addContentTags($menu_login);

    if ($pricing) {
      $this->addContentTags($pricing);
    }

    if ($register)
      $this->addContentTags($register);

    $this->tips = $tips ? $tips->getValue() : '';
    $this->tips_title = $tips_title ? $tips_title->getValue() : '';

    $this->faq = $faq ? $faq->getValue() : '';
    $this->faq_title = $faq_title ? $faq_title->getValue() : '';

    $this->help = $help ? $help->getValue() : '';
    $this->help_title = $help_title ? $help_title->getValue() : '';

    $this->privacy = $privacy ? $privacy->getValue() : '';
    $this->privacy_title = $privacy_title ? $privacy_title->getValue() : '';

    $this->menu_home = $menu_home ? $menu_home->getValue() : '';
    $this->menu_start = $menu_start ? $menu_start->getValue() : '';
    $this->menu_join = ($menu_join ? $menu_join->getValue() : '') && ($register ? $register->getValue() : '');
    $this->menu_login = $menu_login ? $menu_login->getValue() : '';

    $this->pricing = $pricing ? $pricing->getValue() : '';
  }

  public function executeFooter() {
    $store = StoreTable::getInstance();
    $terms = $store->findByKeyCached(StoreTable::TERMS_FOOTER);
    $terms_title = $store->findByKeyCached(StoreTable::TERMS_TITLE);
    $contact = $store->findByKeyCached(StoreTable::CONTACT_FOOTER);
    $contact_title = $store->findByKeyCached(StoreTable::CONTACT_TITLE);
    $privacy = $store->findByKeyCached(StoreTable::PRIVACY_FOOTER);
    $privacy_title = $store->findByKeyCached(StoreTable::PRIVACY_TITLE);
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
    if ($privacy_title)
      $this->addContentTags($privacy_title);
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
    $this->privacy = $privacy ? $privacy->getValue() : '';
    $this->privacy_title = $privacy_title ? $privacy_title->getValue() : '';
    $this->imprint = $imprint ? $imprint->getValue() : '';
    $this->imprint_title = $imprint_title ? $imprint_title->getValue() : '';
    $this->footer_title = $footer_title ? $footer_title->getValue() : '';
    $this->footer_link = $footer_link ? $footer_link->getValue() : '';
  }

}
