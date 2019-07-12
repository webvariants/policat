<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetTable extends Doctrine_Table {

  const DATA_OWNER_YES = 1;
  const DATA_OWNER_NO = 0;
  const FILTER_CAMPAIGN = 'c';
  const FILTER_PETITION = 'a';
  const FILTER_STATUS = 's';
  const FILTER_LANGUAGE = 'l';
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
  const ORDER_WIDGET_ASC = '7';
  const ORDER_WIDGET_DESC = '8';
  const ORDER_LANGUAGE_ASC = '9';
  const ORDER_LANGUAGE_DESC = '10';
  const ORDER_ACTIVITY_ASC = '11';
  const ORDER_ACTIVITY_DESC = '12';
  const ORDER_TRENDING = '13';

  public static $STYLE_COLOR_NAMES = array(
      'title_color',
      'body_color',
      'button_color',
      'bg_left_color',
      'bg_right_color',
      'form_title_color',
      'button_primary_color',
      'label_color'
  );

  public static $STYLE_COLOR_NAMES_CSS = array(
      'title_color' => 'title-color',
      'body_color' => 'text-color',
      'button_color' => 'button-color',
      'bg_left_color' => 'background-color-box',
      'bg_right_color' => 'background-color',
      'form_title_color' => 'heading-color',
      'button_primary_color' => 'sign-button-color',
      'label_color' => 'label-color'
  );

  /**
   *
   * @return WidgetTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('Widget');
  }

  /**
   *
   * @return Doctrine_Query
   */
  public function queryAll() {
    return $this->createQuery('w')->orderBy('w.activity_at DESC');
  }

  /**
   *
   * @param Petition $petition
   * @return Doctrine_Query
   */
  public function queryByPetition(Petition $petition) {
    return $this->queryAll()->where('w.petition_id = ?', $petition->getId());
  }

  public function updateByEmailToUser(sfGuardUser $user) {
    $email = $user->getUsername();
    return $this->createQuery()
        ->update()
        ->where('email = ?', $email)
        ->andWhere('user_id IS NULL')
        ->set('user_id', $user->getId())
        ->execute();
  }

  /**
   *
   * @param Petition $petition
   * @return array
   */
  public function fetchIdsByPetition(Petition $petition) {
    return (array) $this->queryAll()
        ->where('w.petition_id = ?', $petition->getId())
        ->orderBy('w.id ASC')
        ->select('w.id')
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  public function queryByUser(sfGuardUser $user) {
    if ($user->hasPermission(myUser::CREDENTIAL_ADMIN))
      return $this->queryAll()
          ->innerJoin('w.Petition p')
          ->innerJoin('p.Campaign c')
          ->where('p.status != ? AND c.status = ?', array(Petition::STATUS_DELETED, CampaignTable::STATUS_ACTIVE));

    return $this->queryAll()
        ->innerJoin('w.Petition p')->leftJoin('p.PetitionRights pr ON p.id = pr.petition_id and pr.user_id = ?', $user->getId())
        ->innerJoin('p.Campaign c')->leftJoin('c.CampaignRights cr ON c.id = cr.campaign_id and cr.user_id = ?', $user->getId())
        ->where('p.status != ? AND c.status = ?', array(Petition::STATUS_DELETED, CampaignTable::STATUS_ACTIVE))
        ->andWhere('w.user_id = ? OR (cr.user_id = ? AND pr.user_id = ? AND cr.active = 1 AND pr.active = 1 AND pr.member = 1 AND (cr.admin = 1 OR cr.member = 1))', array($user->getId(), $user->getId(), $user->getId()))
    ;
  }

  public function countByPetition(Petition $petition, $timeToLive = 600) {
    $query = $this->queryAll()
      ->where('w.petition_id = ? AND w.status = ?', array($petition->getId(), Widget::STATUS_ACTIVE));
    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    return $query->count();
  }

  /**
   *
   * @param int $id
   * @return Widget
   */
  public function fetch($id, $all_active = true, $timeToLive = 600) {
    $widget = $this->createQuery('w')
      ->addFrom('w.PetitionText pt')
      ->addFrom('w.Petition p')
      ->addFrom('p.CountryCollection pcol')
      ->addFrom('w.Campaign c')
      ->addFrom('p.MailingList ml')
      ->where('w.id = ?', $id)
      ->useResultCache(true, $timeToLive)
      ->fetchOne();

    /* @var $widget Widget */

    if ($widget && $all_active) {
      if ($widget->getStatus() != Widget::STATUS_ACTIVE || $widget->getCampaign()->getStatus() != CampaignTable::STATUS_ACTIVE || $widget->getPetition()->getStatus() != Petition::STATUS_ACTIVE || $widget->getPetitionText()->getStatus() != PetitionText::STATUS_ACTIVE) {
        return null;
      }
    }

    return $widget;
  }

  public function fetchStatus($id, $min_date = null, $max_date = null, $countries = null, $select = array('stylings', 'petition_signings', 'petition_signings24', 'widget_signings', 'widget_signings24')) {
    // construct the date filter
    $date_filter = array();

    if ($min_date > 0) {
      $date_filter[] = 'created_at >= "' . gmdate('Y-m-d H:i:s', $min_date) . '"';
    }

    if ($max_date > 0) {
      $date_filter[] = 'created_at <= "' . gmdate('Y-m-d H:i:s', $max_date) . '"';
    }

    $date_filter = implode(' AND ', $date_filter);

    if ($date_filter) {
      $date_filter = ' AND (' . $date_filter . ')';
    }

    $country_filter = '';

    if ($countries) {
      $country_filter .= ' AND country IN ("' . implode('","', $countries) . '")';
    }

    $query = $this->createQuery('w')
      ->where('w.id = ?', $id)
      ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
      ->leftJoin('w.Petition p')
      ->andWhere('p.status = ?', Petition::STATUS_ACTIVE)
      ->select('w.id as widget_id');

    if (in_array('stylings', $select)) {
      $query->addSelect('w.stylings as stylings');
    }
    if (in_array('petition_signings', $select)) {
      $query->addSelect('((SELECT count(s.id) FROM PetitionSigning s WHERE s.petition_id = p.id and s.status = ' . PetitionSigning::STATUS_COUNTED . $date_filter . $country_filter . ') + (SELECT p.addnum FROM Petition p2 where p2.id = p.id)) as petition_signings');
    }
    if (in_array('petition_signings24', $select)) {
      $query->addSelect('(SELECT count(z.id) FROM PetitionSigning z WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z.created_at  and z.petition_id = p.id and z.status = ' . PetitionSigning::STATUS_COUNTED . $country_filter . ') as petition_signings24');
    }
    if (in_array('widget_signings', $select)) {
      $query->addSelect('((SELECT count(s2.id) FROM PetitionSigning s2 WHERE s2.widget_id = w.id and s2.status = ' . PetitionSigning::STATUS_COUNTED . $date_filter . $country_filter . ')) as widget_signings');
    }
    if (in_array('widget_signings24', $select)) {
      $query->addSelect('(SELECT count(z2.id) FROM PetitionSigning z2 WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z2.created_at  and z2.widget_id = w.id and z2.status = ' . PetitionSigning::STATUS_COUNTED . $country_filter . ') as widget_signings24');
    }

    return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
  }

  /**
   *
   * @param Doctrine_Query $query
   * @param FilterWidgetForm $filter
   * @return Doctrine_Query
   */
  public function filter(Doctrine_Query $query, $filter) {
    if (!$filter)
      return $query;

//    print_r($filter->getValues()); die('y');

    /* @var $filter  policatFilter */

    if ($filter->getValue(self::FILTER_CAMPAIGN)) {
      $query->andWhere('w.campaign_id = ?', $filter->getValue(self::FILTER_CAMPAIGN));
    }

    if ($filter->getValue(self::FILTER_STATUS))
      $query->andWhere('w.status = ?', $filter->getValue(self::FILTER_STATUS));

    if ($filter->getValue(self::FILTER_LANGUAGE))
      $query
        ->leftJoin('w.PetitionText pt_for_lang')
        ->andWhere('pt_for_lang.language_id = ?', $filter->getValue(self::FILTER_LANGUAGE));

    $petition_joined = false;
    if ($filter->getValue(self::FILTER_START)) {
      if (!$petition_joined) {
        $query->leftJoin('w.Petition p_filter');
        $petition_joined = true;
      }
      $query->andWhere('p_filter.start_at > ?', $filter->getValue(self::FILTER_START));
    }

    if ($filter->getValue(self::FILTER_END)) {
      if (!$petition_joined) {
        $query->leftJoin('w.Petition p_filter');
        $petition_joined = true;
      }
      $query->andWhere('p_filter.end_at < ?', $filter->getValue(self::FILTER_END));
    }

    if ($filter->getValue(self::FILTER_PETITION)) {
      $query->andWhere('w.petition_id = ?', $filter->getValue(self::FILTER_PETITION));
    }

    if ($filter->getValue(self::FILTER_ORDER)) {
      switch ($filter->getValue(self::FILTER_ORDER)) {
        case self::ORDER_CAMPAIGN_ASC:
          $query->leftJoin('w.Campaign c_order')->orderBy('c_order.name ASC')->addOrderBy('w.campaign_id ASC')->addOrderBy('w.id ASC');
          break;
        case self::ORDER_CAMPAIGN_DESC:
          $query->leftJoin('w.Campaign c_order')->orderBy('c_order.name DESC')->addOrderBy('w.campaign_id DESC')->addOrderBy('w.id DESC');
          break;
        case self::ORDER_ACTION_ASC:
          if (!$petition_joined) {
            $query->leftJoin('w.Petition p_filter');
            $petition_joined = true;
          }
          $query->orderBy('p_filter.name ASC')->addOrderBy('w.petition_id ASC')->addOrderBy('w.id ASC');
          break;
        case self::ORDER_ACTION_DESC:
          if (!$petition_joined) {
            $query->leftJoin('w.Petition p_filter');
            $petition_joined = true;
          }
          $query->orderBy('p_filter.name DESC')->addOrderBy('w.petition_id DESC')->addOrderBy('w.id DESC');
          break;
        case self::ORDER_STATUS_ASC:
          $query->orderBy('w.status ASC')->addOrderBy('w.id ASC');
          break;
        case self::ORDER_STATUS_DESC:
          $query->orderBy('w.status DESC')->addOrderBy('w.id DESC');
          break;
        case self::ORDER_WIDGET_ASC:
          $query->orderBy('w.id ASC');
          break;
        case self::ORDER_WIDGET_DESC:
          $query->orderBy('w.id DESC');
          break;
        case self::ORDER_LANGUAGE_ASC:
          $query->leftJoin('w.PetitionText pt_ord_lang')->leftJoin('pt_ord_lang.Language ord_lang')->orderBy('ord_lang.order_number ASC')->addOrderBy('w.id ASC');
          break;
        case self::ORDER_LANGUAGE_DESC:
          $query->leftJoin('w.PetitionText pt_ord_lang')->leftJoin('pt_ord_lang.Language ord_lang')->orderBy('ord_lang.order_number DESC')->addOrderBy('w.id DESC');
          break;
        case self::ORDER_ACTIVITY_ASC:
          $query->orderBy('w.activity_at ASC');
          break;
        case self::ORDER_ACTIVITY_DESC:
          $query->orderBy('w.activity_at DESC');
          break;
        case self::ORDER_TRENDING:
          $query->select('w.*');
          $query->orderBy('cron_signings24 DESC, w.activity_at DESC, w.id DESC');
          break;
      }
    }

    if ($filter->getValue(self::FILTER_MIN_SIGNINGS)) {
      $query->andWhere('(SELECT count(ps.id) FROM PetitionSigning ps WHERE ps.widget_id = w.id AND ps.status = ? LIMIT ' . $filter->getValue(self::FILTER_MIN_SIGNINGS) . ') >= ?', array(PetitionSigning::STATUS_COUNTED, $filter->getValue(self::FILTER_MIN_SIGNINGS)));
    }

    return $query;
  }

  public function fetchIdsOfPetition(Petition $petition) {
    $query = $this->createQuery('w')
      ->where('w.petition_id = ?', $petition->getId())
      ->orderBy('w.id ASC')
      ->select('w.id');

    $ids = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    $query->free();
    return array_map('reset', $ids);
  }

  public function fetchOriginIdsOfPetition(Petition $petition) {
    $query = $this->createQuery('w')
      ->where('w.petition_id = ?', $petition->getId())
      ->orderBy('w.id ASC')
      ->select('w.origin_widget_id');

    $ids = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    $query->free();
    return array_map('reset', $ids);
  }

  public function fetchWidgetIdByOrigin($petition_id, $origin_id) {
    $query = $this->createQuery('w')
      ->where('w.petition_id = ? AND w.origin_widget_id = ?', array($petition_id, $origin_id))
      ->select('w.id');

    $id = $query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    return $id ? : false;
  }

}
