<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class PetitionTable extends Doctrine_Table {

  const FILTER_CAMPAIGN = 'c';
  const FILTER_KIND = 'k';
  const FILTER_STATUS = 's';
  const FILTER_START = 't1';
  const FILTER_END = 't2';
  const FILTER_ORDER = 'o';
  const FILTER_MIN_SIGNINGS = 'm';
  const ORDER_CAMPAIGN_ASC = '1';
  const ORDER_CAMPAIGN_DESC = '2';
  const ORDER_ACTION_ASC = '3';
  const ORDER_ACTION_DESC = '4';
  const ORDER_STATUS_ASC = '5';
  const ORDER_STATUS_DESC = '6';
  const ORDER_ID_ASC = '7';
  const ORDER_ID_DESC = '8';
  const ORDER_ACTIVITY_ASC = '9';
  const ORDER_ACTIVITY_DESC = '10';
  const ORDER_TRENDING = '11';
  const KEYWORD_PERSONAL_SALUTATION = '#PERSONAL-SALUTATION#';
  const INDIVIDUALISE_ALL = 1;
  const INDIVIDUALISE_DESIGN = 2;
  const INDIVIDUALISE_NOTHING = 3;

  public static $INDIVIDUALISE = array(
      self::INDIVIDUALISE_ALL => 'Widget owners may individualise texts and design of their widgets',
      self::INDIVIDUALISE_DESIGN => 'Widget owners may only individualise the design but not the texts',
      self::INDIVIDUALISE_NOTHING => 'Widgets texts and designs cannot be individualised'
  );

  const LABEL_MODE_PETITION = 1;
  const LABEL_MODE_INITIATIVE = 2;

  public static $LABEL_MODE = array(
      self::LABEL_MODE_PETITION => 'Petition',
      self::LABEL_MODE_INITIATIVE => 'Citizen initiative'
  );

  /**
   *
   * @return PetitionTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('Petition');
  }

  /**
   * only used by generator
   * @param Doctrine_Query $query
   * @return \Doctrine_Query
   */
  public function adminList(Doctrine_Query $query) {
    $root = $query->getRootAlias();
    $query->leftJoin("$root.Campaign c")->addSelect("$root.*, c.*");
    return $query;
  }

  /**
   *
   * @return Doctrine_Query
   */
  public function queryAll($deleted_too = false) {
    $query = self::getInstance()->createQuery('p')->orderBy('p.activity_at DESC');

    if (!$deleted_too)
      $query->andWhere('p.status != ?', Petition::STATUS_DELETED);

    return $query;
  }

  /**
   *
   * @param int $id
   * @param bool $deleted_too
   * @param bool $check_campaign_deleted
   * @return Petition
   */
  public function findById($id, $deleted_too = false, $check_campaign_deleted = true) {
    if (!is_numeric($id))
      return false;

    $query = $this->queryAll($deleted_too)->andWhere('p.id = ?', $id);

    $res = $query->fetchOne(); /* @var $res Petition */
    $query->free();

    if (!$deleted_too && $check_campaign_deleted && $res && $res->getCampaign()->getStatus() == CampaignTable::STATUS_DELETED)
      return false;

    return $res;
  }

  /**
   *
   * @param int $id
   * @return Petition
   */
  public function findByIdCachedActive($id, $timeToLive = 600) {
    if (!is_numeric($id))
      return false;

    $query = $this->createQuery('p')
      ->where('p.id = ?', $id)
      ->andWhere('p.status = ?', Petition::STATUS_ACTIVE)
      ->leftJoin('p.Campaign c')
      ->andWhere('c.status = ?', CampaignTable::STATUS_ACTIVE)
      ->useResultCache(true, $timeToLive);

    $res = $query->fetchOne(); /* @var $res Petition */
    $query->free();

    return $res;
  }

  /**
   *
   * @param sfGuardUser $user
   * @return Doctrine_Query
   */
  public function queryByUserCampaigns(sfGuardUser $user, $fetch_rights = false, $deleted_too = false, $actionsOfUser = null) {
    $admin = $user->hasPermission(myUser::CREDENTIAL_ADMIN);
    if ($admin) {
      $query = $this->queryAll($deleted_too);
      if (!$deleted_too)
        $query->leftJoin('p.Campaign c')->andWhere('c.status = ?', CampaignTable::STATUS_ACTIVE);
    }
    else {
      $query = $this->queryAll($deleted_too)->leftJoin('p.Campaign c')->innerJoin('c.CampaignRights cr')
        ->andWhere('cr.user_id = ? AND cr.active = 1 AND (cr.member = 1 OR cr.admin = 1)', $user->getId());
      if (!$deleted_too)
        $query->andWhere('c.status = ?', CampaignTable::STATUS_ACTIVE);
    }


    $joined_action_rights = false;

    if ($fetch_rights && !$admin) {
      $query
        ->leftJoin('p.PetitionRights pr')
        ->andWhere('pr.user_id = ? OR pr.user_id is null', $user->getId())
        ->select('p.*, c.*, cr.*, pr.*');

      $joined_action_rights = true;
    }

    if ($actionsOfUser === true && !$admin) {
      if (!$joined_action_rights) {
        $query
          ->leftJoin('p.PetitionRights pr')
          ->andWhere('pr.user_id = ? OR pr.user_id is null', $user->getId());
      }

      $query->andWhere('pr.active = 1 AND (pr.member = 1 OR pr.admin = 1)');
    }

    return $query;
  }

  /**
   *
   * @param Campaign $campaign
   * @return Doctrine_Query
   */
  public function queryByCampaign(Campaign $campaign, $deleted_too = false) {
    return $this->queryAll($deleted_too)->andWhere('p.campaign_id = ?', $campaign->getId());
  }

  public function countActionsByTargetList(MailingList $ml, $deleted_too = false) {
    return $this->queryAll($deleted_too)
        ->where('p.mailing_list_id = ?', $ml->getId())
        ->count();
  }

  public function countActionsDeletedByTargetList(MailingList $ml) {
    return $this->queryAll(true)
        ->where('p.mailing_list_id = ?', $ml->getId())
        ->andWhere('p.status = ?', Petition::STATUS_DELETED)
        ->count();
  }

  /**
   *
   * @param Doctrine_Query $query
   * @param FilterPetitionForm $filter
   * @return Doctrine_Query
   */
  public function filter(Doctrine_Query $query, $filter) {
    if (!$filter)
      return $query;

    /* @var $filter policatFilter */

    if ($filter->getValue(self::FILTER_CAMPAIGN))
      $query->andWhere('p.campaign_id = ?', $filter->getValue(self::FILTER_CAMPAIGN));

    if ($filter->getValue(self::FILTER_KIND))
      $query->andWhere('p.kind = ?', $filter->getValue(self::FILTER_KIND));

    if ($filter->getValue(self::FILTER_STATUS))
      $query->andWhere('p.status = ?', $filter->getValue(self::FILTER_STATUS));
    else
      $query->andWhere('p.status != ?', Petition::STATUS_DELETED);

    if ($filter->getValue(self::FILTER_START)) {
      $query->andWhere('p.start_at > ?', $filter->getValue(self::FILTER_START));
    }

    if ($filter->getValue(self::FILTER_END)) {
      $query->andWhere('p.end_at < ?', $filter->getValue(self::FILTER_END));
    }

    if ($filter->getValue(self::FILTER_ORDER)) {
      switch ($filter->getValue(self::FILTER_ORDER)) {
        case self::ORDER_CAMPAIGN_ASC:
          $query
            ->leftJoin('p.Campaign c_order')
            ->orderBy('c_order.name ASC')->addOrderBy('p.campaign_id ASC')->addOrderBy('p.id ASC');
          break;
        case self::ORDER_CAMPAIGN_DESC:
          $query
            ->leftJoin('p.Campaign c_order')
            ->orderBy('c_order.name DESC')->addOrderBy('p.campaign_id DESC')->addOrderBy('p.id DESC');
          break;
        case self::ORDER_ACTION_ASC:
          $query->orderBy('p.name ASC');
          break;
        case self::ORDER_ACTION_DESC:
          $query->orderBy('p.name DESC');
          break;
        case self::ORDER_STATUS_ASC:
          $query->orderBy('p.status ASC')->addOrderBy('p.id ASC');
          break;
        case self::ORDER_STATUS_DESC:
          $query->orderBy('p.status DESC')->addOrderBy('p.id DESC');
          break;
        case self::ORDER_ID_ASC:
          $query->orderBy('p.id ASC');
          break;
        case self::ORDER_ID_DESC:
          $query->orderBy('p.id DESC');
          break;
        case self::ORDER_ACTIVITY_ASC:
          $query->orderBy('p.activity_at ASC');
          break;
        case self::ORDER_ACTIVITY_DESC:
          $query->orderBy('p.activity_at DESC');
          break;
        case self::ORDER_TRENDING:
          $query->select('p.*');
          $query->addSelect('(SELECT count(z.id) FROM PetitionSigning z WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z.created_at  and z.petition_id = p.id and z.status = ' . PetitionSigning::STATUS_VERIFIED . ') as signings24');
          $query->orderBy('signings24 DESC, p.activity_at DESC, p.id DESC');
          break;
      }
    }

    if ($filter->getValue(self::FILTER_MIN_SIGNINGS)) {
      $query->andWhere('(SELECT count(ps.id) FROM PetitionSigning ps WHERE ps.petition_id = p.id AND ps.status = ? LIMIT ' . $filter->getValue(self::FILTER_MIN_SIGNINGS) . ') >= ?', array(PetitionSigning::STATUS_VERIFIED, $filter->getValue(self::FILTER_MIN_SIGNINGS)));
    }

    return $query;
  }

  /**
   *
   * @return Doctrine_Collection
   */
  public function fetchScheduleNeed() {
    $time = time();
    $today = gmdate('Y-m-d', $time);
    $tomorrow = gmdate('Y-m-d', $time + 24 * 60 * 60);
    return $this->queryAll()
        ->andWhere('p.start_at IS NOT NULL OR p.end_at IS NOT NULL')
        ->andWhere('p.start_at = ? OR p.end_at = ? OR p.end_at = ?', array($today, $today, $tomorrow))
        ->execute();
  }

}
