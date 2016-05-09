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

  const HOTTEST = 'hottest'; // Trending
  const LARGEST = 'largest'; // Popular
  const RECENT = 'recent'; // New

  protected function getPetitions($type) {
    $query = PetitionTable::getInstance()
      ->createQuery('p')
      ->where('p.status = ?', Petition::STATUS_ACTIVE)
      ->andWhere('p.homepage = 1')
      ->leftJoin('p.Campaign c')
      ->andWhere('c.status = ?', CampaignTable::STATUS_ACTIVE)
      ->leftJoin('p.PetitionText pt')
      ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
      ->andWhere('pt.language_id = ?', 'en')
      ->leftJoin('pt.DefaultWidget w')
      ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
      ->select('p.*, pt.*, w.*')
      ->addSelect('(SELECT count(z.id) FROM PetitionSigning z WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z.created_at  and z.petition_id = p.id and z.status = ' . PetitionSigning::STATUS_COUNTED . ') as signings24')
      ->limit(5);

    switch ($type) {
      case self::LARGEST:
        $query->addSelect('((SELECT count(s.id) FROM PetitionSigning s WHERE s.petition_id = p.id and s.status = ' . PetitionSigning::STATUS_COUNTED . ') + (SELECT p.addnum FROM Petition p2 where p2.id = p.id)) as signings');
        $query->orderBy('signings DESC, p.id ASC');
        break;
      case self::RECENT:
        $query->orderBy('p.created_at DESC, p.id ASC');
        break;
      case self::HOTTEST:
      default:
        $query->orderBy('signings24 DESC, p.id ASC');
        break;
    }

    return
      $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
  }

  public function executeOpen_actions() {
    // Javascript vars
    $this->tags = array('policat' => '');  // UtilRegistry::get('twitter_tags')
    $this->widget_styles = array();

    // Open petitions
    $this->open = array();
    foreach (array(self::HOTTEST => 'Trending', self::LARGEST => 'Popular', self::RECENT => 'New') as $key => $value) {
      $data = $this->getPetitions($key);
      $this->tags[$key] = '';
      foreach ($data as $k => &$petition) {
        if ($key == self::HOTTEST && $petition['signings24'] < 1) {
          unset($data[$k]);
          continue;
        }
        
        if ($key == self::LARGEST && $petition['signings'] < 10) {
          unset($data[$k]);
          continue;
        }
        
        $count = PetitionSigningTable::getInstance()->countByPetition($petition['id'], null, null, 60);
        $count += PetitionApiTokenTable::getInstance()->sumOffsets($petition['id'], 60);
        $count += $petition['addnum'];

        $petition['signings'] = $count;
        $text = $petition['PetitionText'][0];
        $widget = $text['DefaultWidget'];
        $style = json_decode($widget['stylings'], true);

        $tags = trim($petition['twitter_tags']);
        if ($tags) {
          $this->tags[$key] .= ($this->tags[$key] ? ' OR ' : '') . $petition['twitter_tags'];
        }

        if (!isset($this->widget_styles[$widget['id']])) {
          $this->widget_styles[$widget['id']] = array(
              'width' => $style['width'],
              'body_color' => '#818286',
              'count' => number_format($petition['signings'], 0, '.', ',') . ' people so far',
              'target' => $petition['signings'] . '-' . Petition::calcTarget($petition['signings'], $petition['target_num']),
              'url' => $this->getContext()->getRouting()->generate('sign', array('id' => $widget['id'], 'hash' => Widget::calcLastHash(
                    $widget['id'], array(
                      $petition['object_version'],
                      $widget['object_version'],
                      $text['object_version']
                  ))), true)
          );
        }
      }
      
      if (count($data)) {
        $this->open[$key] =
          array(
              'title' => $value,
              'data' => $data
        );
//        $this->js['tags'][$key] .= ($this->js['tags'][$key] ? ' OR ' : '') . $this->js['tags']['policat'];
      }
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

  public function executeFeature() {
    $title = trim(StoreTable::value(StoreTable::PORTAL_HOME_TITLE, ''));
    $this->title = $title ? $title : 'Featured action';

    $markup = trim(StoreTable::value(StoreTable::PORTAL_HOME_MARKUP, ''));
    if ($markup) {
      $markup = preg_replace_callback('/#KEYVISUAL-(\d+)#/', array($this, 'getKeyvisualUrl'), $markup);
      $markup = preg_replace('/#WIDGET-(\d+)#/', 'PasjhkX\\1KmsownedS', $markup); // prevent markdown messing up widget
      $markup = UtilMarkdown::transform($markup, true, true);
      $this->markup = preg_replace_callback('/PasjhkX(\d+)KmsownedS/', array('UtilWidget', 'renderWidget'), $markup);
      return;
    }

    $id = PetitionTable::getInstance()
      ->createQuery('p')
      ->where('p.status = ?', Petition::STATUS_ACTIVE)
      ->andWhere('p.homepage = 1')
      ->leftJoin('p.Campaign c')
      ->andWhere('c.status = ?', CampaignTable::STATUS_ACTIVE)
      ->leftJoin('p.PetitionText pt')
      ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
      ->andWhere('pt.language_id = ?', 'en')
      ->leftJoin('pt.DefaultWidget w')
      ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
      ->select('p.id')
      ->addSelect('(SELECT count(z.id) FROM PetitionSigning z WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z.created_at  and z.petition_id = p.id and z.status = ' . PetitionSigning::STATUS_COUNTED . ') as signings24')
      ->limit(1)
      ->orderBy('signings24 DESC, p.id ASC')
      ->fetchArray();

    if ($id) {
      $id = $id[0]['id'];
      $petition =
        PetitionTable::getInstance()
        ->createQuery('p')
        ->where('p.id = ?', $id)
        ->andWhere('p.status = ?', Petition::STATUS_ACTIVE)
        ->andWhere('p.homepage = 1')
        ->leftJoin('p.Campaign c')
        ->andWhere('c.status = ?', CampaignTable::STATUS_ACTIVE)
        ->leftJoin('p.PetitionText pt')
        ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
        ->andWhere('pt.language_id = ?', 'en')
        ->leftJoin('pt.DefaultWidget w')
        ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
        ->select('p.name, p.object_version, p.kind, p.language_id, p.twitter_tags, p.key_visual, p.read_more_url, pt.id, pt.object_version, pt.title, pt.target, pt.body, pt.footer, pt.email_subject, pt.email_body, w.id, w.object_version, w.title, w.target, w.intro, w.footer, w.email_subject, w.email_body, w.stylings')
        ->fetchOne();
      if ($petition) {
        /* @var $petition Petition */
        $text = $petition['PetitionText'][0];
        $widget = $text['DefaultWidget'];
        $url = $this->generateUrl('sign_hp', array('id' => $widget['id'], 'hash' => $widget->getLastHash(true)), true);

        $this->count = $petition->getCount(60);
        $this->target = $this->count . '-' . Petition::calcTarget($this->count, $petition->getTargetNum());

        $this->widget_id = $widget['id'];
        $this->stylings = json_decode($widget->getStylings(), true);
        $this->stylings['type'] = 'embed';
        $this->stylings['url'] = $url;
        $this->stylings['width'] = 'auto';
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