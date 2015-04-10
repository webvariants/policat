<?php

/**
 * Petition
 *
 * @package    policat
 * @subpackage model
 * @author     Martin
 */
class Petition extends BasePetition {

  const STATUS_DRAFT = 1;
//  const STATUS_WAITING = 2;
//  const STATUS_APPROVED = 3;
  const STATUS_ACTIVE = 4;
  const STATUS_BLOCKED = 5;
//  const STATUS_DENIED = 6;
  const STATUS_DELETED = 7;
  const KIND_OLD_PETITION = 1;
  const KIND_PETITION_WITH_COMMENTS = 2;
  const KIND_CITIZEN_INITIATIVE = 3;
  const KIND_OLD_EMAIL_ACTION = 4;
  const KIND_MESSENGER = 5;
  const KIND_NETWORK_NEWS = 6;
  const KIND_GEO = 7;
  const KIND_GEO_EXTRA = 8;
  const KIND_PETITION = 9;
  const KIND_EMAIL_TO_LIST = 10;
  const KIND_EMAIL_ACTION = 11;
  const KIND_PLEDGE = 12;
  const NAMETYPE_SPLIT = 1;
  const NAMETYPE_FULL = 2;
  const FIELD_FULLNAME = 'fullname';
  const FIELD_TITLE = 'title';
  const FIELD_FIRSTNAME = 'firstname';
  const FIELD_LASTNAME = 'lastname';
  const FIELD_ADDRESS = 'address';
  const FIELD_CITY = 'city';
  const FIELD_POSTCODE = 'post_code';
  const FIELD_EMAIL = 'email';
  const FIELD_COUNTRY = 'country';
  const FIELD_COMMENT = 'comment';
  const FIELD_PRIVACY = 'privacy';
  const FIELD_SUBSCRIBE = 'subscribe';
  const FIELD_EMAIL_SUBJECT = 'email_subject';
  const FIELD_EMAIL_BODY = 'email_body';
  const FIELD_REF = 'ref';
  const EDITABLE_YES = 1;
  const EDITABLE_NO = 2;

//  const AUTO_GREETING_NO = 0;
//  const AUTO_GREETING_YES = 1;

  static $NEW_KIND = array(self::KIND_PETITION, self::KIND_EMAIL_TO_LIST, self::KIND_EMAIL_ACTION, self::KIND_PLEDGE);
  static $FIELD_SHOW = array(
      self::FIELD_FULLNAME => 'fullname',
      self::FIELD_TITLE => 'title',
      self::FIELD_FIRSTNAME => 'firstname',
      self::FIELD_LASTNAME => 'lastname',
      self::FIELD_EMAIL => 'email',
      self::FIELD_ADDRESS => 'address',
      self::FIELD_CITY => 'city',
      self::FIELD_POSTCODE => 'post_code',
      self::FIELD_COUNTRY => 'country',
      self::FIELD_COMMENT => 'comment',
      self::FIELD_PRIVACY => 'privacy policy accepted',
      self::FIELD_SUBSCRIBE => 'subscribe');
  static $NAMETYPE_SHOW = array
      (
      self::NAMETYPE_SPLIT => 'Title, firstname, lastname',
      self::NAMETYPE_FULL => 'Fullname'
  );
  static $KIND_EXTRA_FIELDS = array
      (
      self::KIND_OLD_PETITION => array(),
      self::KIND_PETITION_WITH_COMMENTS => array(self::FIELD_COMMENT),
      self::KIND_CITIZEN_INITIATIVE => array(self::FIELD_ADDRESS, self::FIELD_CITY, self::FIELD_POSTCODE),
      self::KIND_OLD_EMAIL_ACTION => array(self::FIELD_ADDRESS, self::FIELD_CITY, self::FIELD_POSTCODE),
      self::KIND_MESSENGER => array(self::FIELD_COMMENT),
      self::KIND_NETWORK_NEWS => array(),
      self::KIND_GEO => array(),
      self::KIND_GEO_EXTRA => array(self::FIELD_ADDRESS, self::FIELD_CITY, self::FIELD_POSTCODE),
  );
  static $NAMETYPE_EXTRA_FIELDS = array
      (
      self::NAMETYPE_FULL => array(self::FIELD_FULLNAME),
      self::NAMETYPE_SPLIT => array(self::FIELD_TITLE, self::FIELD_FIRSTNAME, self::FIELD_LASTNAME)
  );
  static $FIELDS_ALWAYS = array(self::FIELD_EMAIL, self::FIELD_PRIVACY, self::FIELD_SUBSCRIBE, self::FIELD_COUNTRY);
  static $STATUS_SHOW = array
      (
      self::STATUS_DRAFT => 'draft',
//      self::STATUS_WAITING => 'waiting',
//      self::STATUS_APPROVED => 'approved',
      self::STATUS_ACTIVE => 'active',
      self::STATUS_BLOCKED => 'blocked',
//      self::STATUS_DENIED => 'denied'
      self::STATUS_DELETED => 'deleted'
  );
  static $KIND_SHOW = array
      (
      self::KIND_PETITION => 'Petition',
      self::KIND_EMAIL_TO_LIST => 'Email-to-list action',
      self::KIND_EMAIL_ACTION => 'Email-Action',
      self::KIND_PLEDGE => 'e-Action Pledge',
      self::KIND_CITIZEN_INITIATIVE => '(old) Citizen Initiative',
      self::KIND_MESSENGER => '(old) Messenger',
      self::KIND_NETWORK_NEWS => '(old) Network News',
      self::KIND_OLD_PETITION => '(old) Petition',
      self::KIND_PETITION_WITH_COMMENTS => '(old) Petition with comments',
      self::KIND_GEO => '(old) Email-to-list action',
      self::KIND_GEO_EXTRA => '(old) Email-to-list action (with full address)',
      self::KIND_OLD_EMAIL_ACTION => '(old) Email-Action'
  );
  static $KIND_SHOW_CREATE = array
      (
      self::KIND_PETITION => 'Petition',
      self::KIND_EMAIL_TO_LIST => 'Email-to-list action',
      self::KIND_EMAIL_ACTION => 'Email-Action',
      self::KIND_PLEDGE => 'e-Action Pledge',
  );
  static $EMAIL_KINDS = array(
      self::KIND_EMAIL_TO_LIST,
      self::KIND_EMAIL_ACTION,
      self::KIND_PLEDGE,
      self::KIND_OLD_EMAIL_ACTION,
      self::KIND_GEO,
      self::KIND_GEO_EXTRA
  );
  static $EDITABLE_SHOW = array(
      self::EDITABLE_YES => 'yes',
      self::EDITABLE_NO => 'no'
  );
//  static $AUTO_GREETING_SHOW = array(
//      self::AUTO_GREETING_NO => 'no',
//      self::AUTO_GREETING_YES => 'yes'
//  );
  static $WITH_ADDRESS_SHOW = array(0 => 'Don\'t ask', 1 => 'Post code and city', 2 => '(Street) address, post code and city');

  public function calcPossibleStatusForUser(sfGuardUser $user) {
    if (!$user || $user->isPetitionAdmin($this))
      return array_keys(self::$STATUS_SHOW);

    if ($this->getStatus() == self::STATUS_BLOCKED)
      return array(self::STATUS_BLOCKED);

    if ($this->getStatus() == self::STATUS_DELETED)
      return array(self::STATUS_DELETED);

    return array(self::STATUS_DRAFT, self::STATUS_ACTIVE);
  }

  public static function calcStatusShow($statuses) {
    $ret = array();
    foreach ($statuses as $status)
      if (isset(self::$STATUS_SHOW[$status]))
        $ret[$status] = self::$STATUS_SHOW[$status];
    return $ret;
  }

  static private $_day_sql = null;

  static private function daySql() {
    if (self::$_day_sql === null) {
      self::$_day_sql = gmdate('Y-m-d');
    }
    return self::$_day_sql;
  }

  public function isBefore() {
    return $this->getStartAt() && $this->getStartAt() > self::daySql();
  }

  public function isAfter() {
    return $this->getEndAt() && $this->getEndAt() < self::daySql();
  }

  public function getStatusName() {
    if ($this->getStatus() == self::STATUS_ACTIVE) {
      if ($this->isBefore())
        return 'ready (' . $this->getStartAt() . ')';
      if ($this->isAfter())
        return 'ended (' . $this->getEndAt() . ')';
      if ($this->getEndAt())
        return self::$STATUS_SHOW[self::STATUS_ACTIVE] . ' (' . $this->getEndAt() . ')';
    }

    return isset(self::$STATUS_SHOW[$this->getStatus()]) ? self::$STATUS_SHOW[$this->getStatus()] : 'unknown';
  }

  public function getAvailableLanguages() {
    return Doctrine_Core::getTable('Language')
        ->createQuery('l')
        ->leftJoin('l.PetitionText pt')
        ->where('pt.petition_id = ?', $this->getId())
        ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
        ->execute();
  }

  public function getAvailablePetitionTextByLanguageId($language_id) {
    return Doctrine_Core::getTable('PetitionText')
        ->createQuery('pt')
        ->where('pt.language_id = ?', $language_id)
        ->andWhere('pt.petition_id = ?', $this->getId())
        ->fetchOne();
  }

  public function getFormfields() {
    if (in_array($this->getKind(), self::$NEW_KIND)) {
      $fields = array_merge(array(self::FIELD_EMAIL, self::FIELD_PRIVACY, self::FIELD_SUBSCRIBE), self::$NAMETYPE_EXTRA_FIELDS[$this->getNametype()]);
      switch ($this->getWithAddress()) {
        case 1:
        case '1':
          $fields[] = self::FIELD_POSTCODE;
          $fields[] = self::FIELD_CITY;
          break;
        case 2:
        case '2':
          $fields[] = self::FIELD_POSTCODE;
          $fields[] = self::FIELD_CITY;
          $fields[] = self::FIELD_ADDRESS;
          break;
      }
      if ($this->getWithCountry()) {
        $fields[] = self::FIELD_COUNTRY;
      }
      if ($this->getWithComments()) {
        $fields[] = self::FIELD_COMMENT;
      }
    } else {
      $fields = array_merge(
        self::$FIELDS_ALWAYS, self::$KIND_EXTRA_FIELDS[$this->getKind()], self::$NAMETYPE_EXTRA_FIELDS[$this->getNametype()]
      );
    }
    $formfields = array();
    foreach (array_keys(self::$FIELD_SHOW) as $field) {
      if (in_array($field, $fields)) {
        $formfields[] = $field;
      }
    }
    return $formfields;
  }

  public function getFrom() {
    return array($this->getFromEmail() => $this->getFromName());
  }

  public function isEmailKind() {
    return in_array((int) $this->getKind(), self::$EMAIL_KINDS, true);
  }

  public function isGeoKind() {
    $kind = $this->getKind();
    return $kind == self::KIND_EMAIL_TO_LIST || $kind == self::KIND_PLEDGE || $kind == self::KIND_GEO || $kind == self::KIND_GEO_EXTRA;
  }

  public function getKindName() {
    return self::$KIND_SHOW[$this->getKind()];
  }

  public function __toString() {
    return $this->getName() . ' (' . $this->getCampaign()->getName() . ')';
  }

  protected $_target_selectors = null;

  /**
   * What you can get inside the array:
   * [id: 'contact', name: 'Recipient(s)', 'choices': contacts_array]                                                                       (no target selectors)
   * [id: meta_id, name: meta_name, choices: values, kind: meta_kind, mapping_id | null, meta_id: meta_id | null, typefield: is_typfield]   (1st target selector)
   * [id: meta_id, name: meta_name, typfield: is_typfield]                                                                                  (2nd target selctor)
   * [id: 'country', name:'Country', choices: countries, country: true]                                                                     (1st target selector)
   * [id: 'country', name:'Country', country: true]                                                                                         (2nd target selctor}
   *
   * @param boolean $fresh
   * @return boolean | array
   */
  public function getTargetSelectors($fresh = false) {
    if ($this->_target_selectors !== null && !$fresh)
      return $this->_target_selectors;
    if ($this->isGeoKind()) {
      $json = $this->getEmailTargets();
      if (is_string($json) && strlen($json)) {
        $selectors = json_decode($json, true);
        $ml_cached = MailingListTable::getInstance()->findAndFetchCached($this->getMailingListId());
        $metas = $ml_cached->getMailingListMeta();
        $this->_target_selectors = array();
        if (empty($selectors)) {
          // NO TARGET SELECTORS
          $choices0 = array();
          $pledges = false;
          $infos = array();
          $pledge_info_columns = array();
          $active_pledge_item_ids = false;
          if ($this->getKind() == Petition::KIND_PLEDGE) {
            $pledges = array();
            $active_pledge_item_ids = $this->getActivePledgeItemIds();
            $pledge_info_columns = $this->getPledgeInfoColumnsArray();
            $contacts = ContactTable::getInstance()->queryByMailingList($ml_cached, $this)->execute();
          } else {
            $contacts = ContactTable::getInstance()->queryByMailingList($ml_cached)->execute();
          }
          $pledge_table = PledgeTable::getInstance();
          foreach ($contacts as $contact) {
            /* @var $contact Contact */
            $choices0[$contact->getId()] = $contact->getFullname();
            if ($active_pledge_item_ids) {
              $contact_pledges = $pledge_table->getPledgesForContact($contact, $active_pledge_item_ids);
              if ($contact_pledges) {
                $pledges[$contact->getId()] = $contact_pledges;
              }
            }
            if ($pledge_info_columns) {
              $infos[$contact->getId()] = $contact->getPledgeInfoColumns($pledge_info_columns);
            }
          }
          $this->_target_selectors[] = array(
              'id' => 'contact',
              'name' => 'Recipient(s)',
              'choices' => $choices0,
              'pledges' => $pledges,
              'infos' => $infos
          );
          //
        } else {
          foreach ($selectors as $selector) {
            if (count($this->_target_selectors) >= 2)
              break;
            if (is_numeric($selector)) {
              foreach ($metas as $meta)
              /* @var $meta MailingListMeta */
                if ($meta['id'] == $selector) {
                  if (empty($this->_target_selectors)) {
                    // FIRST TARGET SELCTOR
                    $choices1 = array();
                    if ($meta['kind'] == MailingListMeta::KIND_CHOICE) {
                      $choices = MailingListMetaChoiceTable::getInstance()->getByMetaIdCached($meta['id'], $this->getMailingListId());
                      foreach ($choices as $choice)
                        $choices1[$choice['id']] = $choice['choice'];
                    } elseif ($meta['kind'] == MailingListMeta::KIND_MAPPING) {
                      $mapping_id = $meta->getMappingId();
                      if ($mapping_id) {
                        $map_keys = MappingPairTable::getInstance()->getAsByMappingId($mapping_id);
                        $choices1 = array_combine($map_keys, $map_keys);
                      }
                    }
                    $this->_target_selectors[] = array(
                        'id' => $meta['id'],
                        'name' => $meta['name'],
                        'choices' => $choices1,
                        'kind' => $meta['kind'],
                        'mapping_id' => $meta['kind'] == MailingListMeta::KIND_MAPPING ? $meta->getMappingId() : null,
                        'meta_id' => $meta['kind'] == MailingListMeta::KIND_MAPPING ? $meta->getMetaId() : null,
                        'typfield' => $meta->getTypfield(),
                    );
                  } else {
                    // SECOND TARGET SELCTOR
                    $this->_target_selectors[] = array(
                        'id' => $meta['id'],
                        'name' => $meta['name'],
                        'typfield' => $meta->getTypfield()
                    );
                  }
                  break;
                }
            } else {
              if ($selector == MailingList::FIX_COUNTRY) {
                if (empty($this->_target_selectors)) {
                  $countries = Doctrine_Core::getTable('Contact')
                    ->createQuery('c')
                    ->where('c.mailing_list_id = ?', $this->getMailingListId())
                    ->groupBy('c.country')
                    ->select('c.country')
                    ->fetchArray();
                  $choices1c = array();
                  foreach ($countries as $country) {
                    if ($country['country']) {
                      $choices1c[$country['country']] = $country['country'];
                    }
                  }
                  $this->_target_selectors[] = array(
                      'id' => $selector,
                      'name' => MailingList::$FIX_SHOW[MailingList::FIX_COUNTRY],
                      'choices' => $choices1c,
                      'country' => true
                  );
                } else {
                  $this->_target_selectors[] = array(
                      'id' => $selector,
                      'name' => MailingList::$FIX_SHOW[MailingList::FIX_COUNTRY],
                      'country' => true
                  );
                }
              }
            }
          }
        }
      } else
        $this->_target_selectors = array();

      return $this->_target_selectors;
    }
    return false;
  }

  public function getTargetSelectorChoices($first) {
    $tagging_cache = sfCacheTaggingToolkit::getTaggingCache();
    $cache_key = 'Petition_TS1_' . $this->getId() . '_' . (is_scalar($first) ? $first : md5(json_encode($first)));
    $cached_ret = $tagging_cache->get($cache_key, null);
    if ($cached_ret !== null) {
      return $cached_ret;
    }

    $ts = $this->getTargetSelectors();
    $is_pledge = $this->getKind() == Petition::KIND_PLEDGE;
    $ret = false;
    if ($ts && is_scalar($first)) {
      if ($is_pledge) {
        $active_pledge_item_ids = $this->getActivePledgeItemIds();
      } else {
        $active_pledge_item_ids = false;
      }

      if (array_key_exists($first, $ts[0]['choices'])) {
        if (array_key_exists('mapping_id', $ts[0]) && $ts[0]['mapping_id']) {
          $mapped = MappingPairTable::getInstance()->getMapByIdAndA($ts[0]['mapping_id'], $first);
          $ret = array('choices' => array(), 'pledges' => false, 'infos' => array());
          foreach ($mapped as $b) {
            $choices_and_pledges = MailingListTable::getInstance()->getChoices($this, $b, $active_pledge_item_ids);
            if ($choices_and_pledges['pledges'] !== false && $ret['pledges'] === false) {
              $ret['pledges'] = array();
            }

            foreach ($choices_and_pledges['choices'] as $k => $v) {
              $ret['choices'][$k] = $v;
              if ($choices_and_pledges['pledges'] !== false) {
                // we got Contacts with Pledges and maybe Infos
                if (array_key_exists($k, $choices_and_pledges['pledges'])) {
                  $ret['pledges'][$k] = $choices_and_pledges['pledges'][$k];
                }
                if (array_key_exists($k, $choices_and_pledges['infos'])) {
                  $ret['infos'][$k] = $choices_and_pledges['infos'][$k];
                }
              }
            }
          }
        } else {
          $ret = MailingListTable::getInstance()->getChoices($this, $first, $active_pledge_item_ids);
        }
      }
    }
    if ($ret === false) {
      array('choices' => array(), 'pledges' => $is_pledge ? array() : false, 'infos' => array());
    }

    $tags = $this->getCacheTags();
    if ($this->getMailingListId()) {
      $tags = array_merge($tags, $this->getMailingList()->getCacheTags());
    }

    $tagging_cache->set($cache_key, $ret, 24 * 3600, $tags);

    return $ret;
  }

  public function getTargetSelectorChoices2($first, $second) {
    $tagging_cache = sfCacheTaggingToolkit::getTaggingCache();
    $cache_key = 'Petition_TS2_' . $this->getId() . '_' . ((is_scalar($first) && is_scalar($second)) ? $first . '__' . $second : md5(json_encode(array($first, $second))));
    $cached_ret = $tagging_cache->get($cache_key, null);
    if ($cached_ret !== null) {
      return $cached_ret;
    }

    $contacts = ContactTable::getInstance()->queryByTargetSelector($this, $first, $second)->execute();
    $pledge_table = PledgeTable::getInstance();
    $choices = array();
    $active_pledge_item_ids = $this->getActivePledgeItemIds();
    $pledge_info_columns = $this->getPledgeInfoColumnsArray();
    $pledges = $pledge_table->getPledgesForContacts($contacts, $active_pledge_item_ids);
    $infos = ContactTable::getInstance()->getPledgeInfoColumns($contacts, $pledge_info_columns);

    foreach ($contacts as $contact) {
      /* @var $contact Contact */
      $choices[$contact['id']] = $contact['firstname'] . ' ' . $contact['lastname'];
    }

    $ret = array('choices' => $choices, 'pledges' => $pledges, 'infos' => $infos);

    $tags = $this->getCacheTags();
    if ($this->getMailingListId()) {
      $tags = array_merge($tags, $this->getMailingList()->getCacheTags());
    }

    $tagging_cache->set($cache_key, $ret, 24 * 3600, $tags);

    return $ret;
  }

  protected function getGeoSubstFields() {
    if ($this->isGeoKind()) {
      if ($this->getMailingListId()) {
        $ml = MailingListTable::getInstance()->findAndFetchCached($this->getMailingListId());
        if ($ml)
          return $ml->getSubstFields();
      }
    }
    return array();
  }

  public static function calcTarget($count, $target_num = 0) {
    if ($count < 0)
      $count = 0;
    if ($count < $target_num)
      return $target_num;
    $targets = array(100, 250, 500, 1000, 2500, 5000, 10000, 20000, 35000, 50000, 75000, 100000, 200000, 500000, 1000000, 2500000, 5000000);
    $target = 1;
    foreach ($targets as $target) {
      if ($count < $target) {
        break;
      }
    }
    return $target;
  }

  public function isEditableBy(sfGuardUser $user) {
    return $user->isPetitionMember($this, true);
  }

  public function isMemberEditable(sfGuardUser $user) {
    return $user->isCampaignAdmin($this->getCampaign()) || $user->isPetitionAdmin($this);
  }

  public function isTicketManager(sfGuardUser $user) {
    return $user->isCampaignAdmin($this->getCampaign()) || $user->isPetitionAdmin($this);
  }

  public function countSignings($timeToLive = 600) {
    return PetitionSigningTable::getInstance()->countByPetition($this, null, null, $timeToLive);
  }

  public function countSigningsPlus($timeToLive = 600) {
    $add = $this->getAddnum();
    if (is_numeric($add))
      return $this->countSignings($timeToLive) + $add;
    return $this->countSignings($timeToLive);
  }

  public function sumApi($timeToLive = 600) {
    return PetitionApiTokenTable::getInstance()->sumOffsets($this, $timeToLive);
  }

  public function countSigningsPlusApi($timeToLive = 600) {
    return $this->countSigningsPlus($timeToLive) + $this->sumApi($timeToLive);
  }

  public function countSignings24() {
    return PetitionSigningTable::getInstance()->count24ByPetition($this);
  }

  public function countSigningsPending() {
    return PetitionSigningTable::getInstance()->countPendingByPetition($this);
  }

  public function countWidgets() {
    return WidgetTable::getInstance()->countByPetition($this);
  }

  public function countMailsSent() {
    return PetitionSigningWaveTable::getInstance()->sumContactStatus(PetitionSigning::STATUS_SENT, $this);
//    return PetitionSigningContactTable::getInstance()->countSentMails($this);
  }

  public function countMailsPending() {
    return PetitionSigningWaveTable::getInstance()->sumContactStatus(PetitionSigning::STATUS_PENDING, $this);
//    return PetitionSigningContactTable::getInstance()->countPendingMails($this);
  }

  public function countMailsOutgoing() {
    return PetitionSigningWaveTable::getInstance()->sumContactStatus(PetitionSigning::STATUS_VERIFIED, $this);
//    return PetitionSigningContactTable::getInstance()->countOutgoingMails($this);
  }

  public function getActivePledgeItemIds() {
    $ids = array();
    foreach ($this->getPledgeItems() as $pledge_item) {
      /* @var $pledge_item PledgeItem */
      if ($pledge_item->getStatus() == PledgeItemTable::STATUS_ACTIVE) {
        $ids[] = $pledge_item->getId();
      }
    }
    return $ids;
  }

  public function getPledgeInfoColumnsComma($allow = null) {
    $value = $this->getPledgeInfoColumns();
    if ($value) {
      $decoded = json_decode($value, true);
      if (is_array($allow)) {
        $decoded = array_intersect($decoded, $allow);
      }
      return implode(',', $decoded);
    }
    return '';
  }

  public function setPledgeInfoColumnsComma($value) {
    $parts = explode(',', $value);
    $json = array();
    foreach ($parts as $part) {
      $part = trim($part);
      if ($part) {
        $json[] = $part;
      }
    }

    $this->setPledgeInfoColumns(json_encode($json));
  }

  public function getPledgeInfoColumnsArray() {
    $value = $this->getPledgeInfoColumns();
    if ($value) {
      return json_decode($value, true);
    }
    return array();
  }

  public function getWidgetIndividualiseText() {
    $indi = $this->getWidgetIndividualise();
    return $indi == PetitionTable::INDIVIDUALISE_ALL;
  }

  public function getWidgetIndividualiseDesign() {
    $indi = $this->getWidgetIndividualise();
    return $indi == PetitionTable::INDIVIDUALISE_ALL || $indi == PetitionTable::INDIVIDUALISE_DESIGN;
  }

  public function getCount($timeToLive = 600, $refresh = false) {
    $count = PetitionSigningTable::getInstance()->countByPetition($this, null, null, $timeToLive, $refresh);
    $count += PetitionApiTokenTable::getInstance()->sumOffsets($this, $timeToLive, $refresh);
    $count += $this->getAddnum();

    return $count;
  }

}
