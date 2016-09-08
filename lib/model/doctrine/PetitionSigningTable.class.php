<?php

class PetitionSigningTable extends Doctrine_Table {

  /**
   *
   * @return PetitionSigningTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('PetitionSigning');
  }

  /**
   *
   * @return Doctrine_Query
   */
  public function queryAll($alias = 'ps') {
    return $this->createQuery($alias)->orderBy($alias . '.id');
  }

  const CAMPAIGN = 'campaign';
  const PETITION = 'petition';
  const LANGUAGE = 'language';
  const COUNTRY = 'country';
  const SUBSCRIBER = 'subscriber';
  const STATUS = 'status';
  const WIDGET = 'widget';
  const USER = 'user';
  const SEARCH = 'search';
  const ORDER = 'order';
  const BOUNCE = 'bounce';
  const DOWNLOAD = 'download';
  const DOWNLOAD_NULL = 'download_null';
  const WIDGET_FILTER = 'widget_filter';
  const KEYWORD_NAME = '#SENDER-NAME#';
  const KEYWORD_COUNTRY = '#SENDER-COUNTRY#';
  const KEYWORD_ADDRESS = '#SENDER-ADDRESS#';
  const KEYWORD_EMAIL = '#SENDER-EMAIL#';
  const KEYWORD_DATE = '#ACTION-TAKEN-DATE#';
  const ORDER_ASC = '1';
  const ORDER_DESC = '2';
  const ORDER_BOUNCE_AT_ASC = '3';
  const ORDER_BOUNCE_AT_DESC = '4';

  static $DEFAULT_OPTIONS = array(
      self::CAMPAIGN => null,
      self::PETITION => null,
      self::LANGUAGE => null,
      self::COUNTRY => null,
      self::SUBSCRIBER => false,
      self::STATUS => PetitionSigning::STATUS_COUNTED,
      self::WIDGET => null,
      self::USER => null,
      self::SEARCH => '',
      self::ORDER => null,
      self::WIDGET_FILTER => '',
      self::BOUNCE => false,
      self::DOWNLOAD => null,
      self::DOWNLOAD_NULL => null,
  );
  static $KEYWORDS = array(
      self::KEYWORD_NAME,
      self::KEYWORD_ADDRESS,
      self::KEYWORD_COUNTRY,
      self::KEYWORD_EMAIL,
      self::KEYWORD_DATE
  );

  /**
   *
   * @param array $options
   * @return Doctrine_Query
   * @throws Exception
   */
  public function query(array $options) {
    $query = $this->queryAll('ps')
      ->leftJoin('ps.Widget w')
      ->leftJoin('w.PetitionText pt')
      ->useQueryCache(false);

    $options = array_merge(self::$DEFAULT_OPTIONS, $options);
    $language = $options[self::LANGUAGE];
    $country = $options[self::COUNTRY];
    $subscriber = $options[self::SUBSCRIBER];
    $campaign = $options[self::CAMPAIGN];
    $petition = $options[self::PETITION];
    $status = $options[self::STATUS];
    $widget = $options[self::WIDGET];
    $user = $options[self::USER];
    $search = $options[self::SEARCH];
    $order = $options[self::ORDER];
    $widget_filter = $options[self::WIDGET_FILTER];
    $bounce = $options[self::BOUNCE];
    $download = $options[self::DOWNLOAD];
    $download_null = $options[self::DOWNLOAD_NULL];

    if ($status) {
      $query->andWhere('ps.status = ?', $status);
    }

    if ($bounce) {
      $query->andWhere('ps.bounce = 1');
      $query->andWhere('ps.verified = ?', PetitionSigning::VERIFIED_NO);
    }

    if ($download) {
      /* @var $download Download */
      if (!$download->getId() || !$download->getIncremental()) {
        throw new Exception('missing download id or not incremental');
      }

      if ($download->getSubscriber()) {
        $query->andWhere('ps.download_subscriber_id = ?', $download->getId());
      } else {
        $query->andWhere('ps.download_data_id = ?', $download->getId());
      }
    }

    if ($download_null !== null) {
      if ($download_null) {
        $query->andWhere('ps.download_subscriber_id IS NULL');
      } else {
        $query->andWhere('ps.download_data_id IS NULL');
      }
    }

    if (!($petition || $campaign || $widget))
      throw new Exception('campaign or petition required');

    if ($petition) {
      if (is_array($petition)) {
        $ids = array();
        foreach ($petition as $p)
          $ids[] = is_object($p) ? $p->getId() : $p;
        $query->andWhereIn('ps.petition_id', $ids);
      } else
        $query->andWhere('ps.petition_id = ?', is_object($petition) ? $petition->getId() : $petition);
    }

    if ($campaign) {
      $query
        ->leftJoin('ps.Petition p')
        ->andWhere('p.status != ?', Petition::STATUS_DELETED);
      if (is_array($campaign)) {
        $ids = array();
        foreach ($campaign as $c)
          $ids[] = is_object($c) ? $c->getId() : $c;
        $query->andWhereIn('p.campaign_id', $ids);
      } else
        $query->andWhere('p.campaign_id = ?', is_object($campaign) ? $campaign->getId() : $campaign);
    }

    if ($widget) {
      $widget_id = is_object($widget) ? $widget->getId() : $widget;
      if (is_array($widget_id))
        $query->andWhereIn('ps.widget_id', $widget_id);
      else
        $query->andWhere('ps.widget_id = ?', $widget_id);
    }

    if ($language) {
      $widget_ids_lang_sub_query = $query->copy()
        ->orderBy('ps.widget_id')
        ->removeSqlQueryPart('orderby')
        ->select('DISTINCT ps.widget_id');
      if (is_array($language)) {
        $widget_ids_lang_sub_query->andWhereIn('pt.language_id', $language);
      } else {
        $widget_ids_lang_sub_query->andWhere('pt.language_id = ?', $language);
      }

      $widget_lang_filter_query = WidgetTable::getInstance()->createQuery('wlfq')
        ->where('wlfq.id IN (' . $widget_ids_lang_sub_query->getDql() . ')', $widget_ids_lang_sub_query)
        ->removeSqlQueryPart('orderby')
        ->select('DISTINCT wlfq.id');
      $widget_lang_filter_query->setParams($widget_ids_lang_sub_query->getParams());



      $widget_lang_ids = (array) $widget_ids_lang_sub_query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (count($widget_lang_ids)) {
        $query->andWhereIn('ps.widget_id', $widget_lang_ids);
      } else {
        $query->andWhere('ps.widget_id = -3'); // force to return nothing
      }
    }

    if ($country) {
      if (is_array($country))
        $query->andWhereIn('ps.country', $country);
      else
        $query->andWhere('ps.country = ?', $country);
    }

    $this->andQuerySubscriber($query, $user, $subscriber, 'ps');

    if ($search) {
      $search_normalized = PetitionSigningSearchTable::normalize($search);
      $likes_dql = array();
      $likes_param = array();
      $i = 0;
      foreach (explode(' ', $search_normalized) as $part) {
        if ($i > 5)
          break;
        $len = mb_strlen($part, 'UTF-8');
        if ($len > 2) {
          if ($len > 48) {
            $part = mb_substr($part, 0, 48, 'UTF-8');
          }
          $i++;
          $query->andWhere('ps.id in (SELECT search' . $i . '.id FROM PetitionSigningSearch search' . $i . ' WHERE search' . $i . '.keyword LIKE ?)', $part . '%');
        }
      }
    }

    switch ($order) {
      case self::ORDER_ASC:
        $query->orderBy('ps.id ASC');
        break;
      case self::ORDER_DESC:
        $query->orderBy('ps.id DESC');
        break;
      case self::ORDER_BOUNCE_AT_ASC:
        $query->orderBy('ps.bounce_at ASC');
        break;
      case self::ORDER_BOUNCE_AT_DESC:
        $query->orderBy('ps.bounce_at DESC');
        break;
    }

    if ($widget_filter) {
      if (preg_match('/^u(\d+)$/', $widget_filter, $matches)) {
        $query->andWhere('w.user_id = ?', $matches[1]);
      } elseif (preg_match('/^w(\d+)$/', $widget_filter, $matches)) {
        $query->andWhere('w.id = ?', $matches[1]);
      } else {
        $query->andWhere('w.organisation = ?', $widget_filter);
      }
    }

    return $query;
  }

  private function andQuerySubscriber($query, $user = null, $subscriber = true, $alias = 'ps') {
    if ($subscriber) {
      $query->andWhere("$alias.subscribe = ?", PetitionSigning::SUBSCRIBE_YES);

      $widget_ids_sub_query = $query->copy()
        ->orderBy("$alias.widget_id")
        ->removeSqlQueryPart('orderby')
        ->select("DISTINCT $alias.widget_id");

      $widget_filter_query = WidgetTable::getInstance()->createQuery('wfq')
        ->where('wfq.id IN (' . $widget_ids_sub_query->getDql() . ')', $widget_ids_sub_query)
        ->removeSqlQueryPart('orderby')
        ->select('DISTINCT wfq.id');
      $widget_filter_query->setParams($widget_ids_sub_query->getParams());

      if ($user) {
        $user_id = is_object($user) ? $user->getId() : $user;
        $widget_filter_query->andWhere('wfq.user_id = ? AND wfq.data_owner = ?', array($user_id, WidgetTable::DATA_OWNER_YES));
      } else {
        $widget_filter_query->andWhere('wfq.user_id is null OR wfq.data_owner = ?', WidgetTable::DATA_OWNER_NO);
      }

      $widget_ids = (array) $widget_filter_query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (count($widget_ids)) {
        $query->andWhereIn("$alias.widget_id", $widget_ids);
      } else {
        $query->andWhere("$alias.widget_id = -1"); // force to return nothing
      }
    }
  }

  public function countByPetition($petition, $min_date = null, $max_date = null, $timeToLive = 600, $refresh = false) {
    $petition_id = $petition instanceof Petition ? $petition->getId() : $petition;
    $query = $this->queryAll('ps')
      ->where('ps.petition_id = ? AND ps.status = ?', array($petition_id, PetitionSigning::STATUS_COUNTED));

    $this->dateFilter($query, $min_date, $max_date, 'ps', 'created_at');

    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    if ($refresh)
      $query->expireResultCache();

    return $query->count();
  }

  public function countByPetitionCountries($petition_id, $min_date = null, $max_date = null, $timeToLive = 600, $refresh = false) {
    $query = $this->queryAll('ps')
        ->where('ps.petition_id = ? AND ps.status = ?', array($petition_id, PetitionSigning::STATUS_COUNTED))
        ->select('ps.country, COUNT(ps.id) as count')->groupBy('ps.country');

    $this->dateFilter($query, $min_date, $max_date, 'ps', 'created_at');

    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    if ($refresh)
      $query->expireResultCache();

    $ret = array();

    foreach ($query->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW) as $row) {
      if (!$row['country']) {
        $ret['unknown'] = (int) $row['count'];
      } else {
        $ret[$row['country']] = (int) $row['count'];
      }
    }

    return $ret;
  }

  public function count24ByPetition(Petition $petition, $timeToLive = 600) {
    $query = $this
      ->queryAll('ps')
      ->where('ps.petition_id = ? AND ps.status = ? AND DATE_SUB(NOW(),INTERVAL 1 DAY) <= ps.created_at', array($petition->getId(), PetitionSigning::STATUS_COUNTED));
    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    return $query->count();
  }

  public function countPendingByPetition(Petition $petition, $timeToLive = 600) {
    $query = $this->queryAll('ps')
      ->where('ps.petition_id = ? AND ps.status = ?', array($petition->getId(), PetitionSigning::STATUS_PENDING));
    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    return $query->count();
  }

  public function countByWidget($widget, $min_date = null, $max_date = null, $timeToLive = 600, $refresh = false) {
    $widget_id = $widget instanceof Widget ? $widget->getId() : $widget;
    $query = $this->queryAll('ps')
      ->where('ps.widget_id = ? AND ps.status = ?', array($widget_id, PetitionSigning::STATUS_COUNTED));
    $this->dateFilter($query, $min_date, $max_date, 'ps', 'created_at');

    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    if ($refresh)
      $query->expireResultCache();

    return $query->count();
  }

  public function countByWidgetCountries($widget_id, $min_date = null, $max_date = null, $timeToLive = 600, $refresh = false) {
    $query = $this->queryAll('ps')
        ->where('ps.widget_id = ? AND ps.status = ?', array($widget_id, PetitionSigning::STATUS_COUNTED))
        ->select('ps.country, COUNT(ps.id) as count')->groupBy('ps.country');
    $this->dateFilter($query, $min_date, $max_date, 'ps', 'created_at');

    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    if ($refresh)
      $query->expireResultCache();

    $ret = array();

    foreach ($query->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW) as $row) {
      if (!$row['country']) {
        $ret['unknown'] = (int) $row['count'];
      } {
        $ret[$row['country']] = (int) $row['count'];
      }
    }

    return $ret;
  }

  public function countPendingByWidget(Widget $widget, $timeToLive = 600) {
    $query = $this->queryAll('ps')
      ->where('ps.widget_id = ? AND ps.status = ?', array($widget->getId(), PetitionSigning::STATUS_PENDING));
    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    return $query->count();
  }

  /**
   *
   * @param int $id
   * @return PetitionSigning
   */
  public function fetch($id) {
    return $this->createQuery('ps')
        ->leftJoin('ps.Petition p')
        ->leftJoin('p.Campaign c')
        ->leftJoin('ps.Widget w, w.PetitionText pt')
        ->leftJoin('ps.PetitionSigningWave psw')
        ->where('ps.id = ?', $id)
        ->select('ps.*, p.id, p.kind, p.email_targets, p.landing_url, ps.widget_id, c.id, c.billing_enabled, c.quota_id, w.id, w.petition_text_id, w.landing_url, pt.id, pt.language_id, pt.landing_url, psw.*')
        ->fetchOne();
  }

  /**
   *
   * @param int $petition_id
   * @param straing $email
   * @param int $lower_then_id
   * @return PetitionSigning
   */
  public function findByPetitionIdAndEmail($petition_id, $email, $lower_then_id = null) {
    $query = $this
      ->createQuery('s')
      ->where('s.petition_id = ?', $petition_id)
      ->andWhere('s.email = ?', $email);
    if ($lower_then_id) {
      $query->andWhere('s.id < ?', $lower_then_id);
    }
    return $query->limit(1)->fetchOne();
  }

  public function getWidgetFilter(Petition $petition) {
    $query_orga = WidgetTable::getInstance()
      ->queryByPetition($petition)
      ->andWhere('w.organisation != ""')
      ->andWhere('w.id IN (SELECT DISTINCT ps.widget_id FROM PetitionSigning ps WHERE ps.status = ? AND ps.widget_id = w.id)', PetitionSigning::STATUS_COUNTED);
    $query_orga->select('DISTINCT ' . $query_orga->getRootAlias() . '.organisation');
    $result_orga = $query_orga->fetchArray();

    $organisations = array();
    foreach ($result_orga as $orga) {
      $organisations[$orga['organisation']] = $orga['organisation'];
    }

    $query_user = WidgetTable::getInstance()
      ->queryByPetition($petition)
      ->andWhere('w.user_id IS NOT NULL')
      ->andWhere('w.id IN (SELECT DISTINCT ps.widget_id FROM PetitionSigning ps WHERE ps.status = ? AND ps.widget_id = w.id)', PetitionSigning::STATUS_COUNTED);
    $query_user->select('DISTINCT ' . $query_user->getRootAlias() . '.user_id');
    $result_user = $query_user->fetchArray();
    $user_ids = array();
    foreach ($result_user as $user_id) {
      $user_ids[] = $user_id['user_id'];
    }

    $users = array();
    if ($user_ids) {
      $user_objs = sfGuardUserTable::getInstance()->queryAll()->andWhereIn('u.id', $user_ids)->execute();
      foreach ($user_objs as $user) {
        /* @var $user sfGuardUser */
        $users['u' . $user->getId()] = $user->getName();
      }
    }

    $widgets = array();
    $widget_ids = WidgetTable::getInstance()
      ->queryByPetition($petition)
      ->andWhere('w.id IN (SELECT DISTINCT ps.widget_id FROM PetitionSigning ps WHERE ps.status = ? AND ps.widget_id = w.id)', PetitionSigning::STATUS_COUNTED)
      ->select('w.id')
      ->fetchArray();
    foreach ($widget_ids as $widget) {
      $widgets['w' . $widget['id']] = 'Widget #' . $widget['id'];
    }

    return array(
        '' => array('' => ''),
        'Organisations' => $organisations,
        'Users' => $users,
        'Widgets' => $widgets
    );
  }

  public function fetchCountries($widget_id, $min_date = null, $max_date = null) {
    if (is_array($widget_id) && count($widget_id) < 1) {
      return array();
    }

    $query = $this->createQuery('ps')
      ->whereIn('ps.widget_id', $widget_id)
      ->andWhere('status = ' . PetitionSigning::STATUS_COUNTED)
      ->select('ps.country')
      ->groupBy('ps.country');

    $this->dateFilter($query, $min_date, $max_date);

    return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
  }

  public function fetchSigningDateRange($widget_id, $min_date = null, $max_date = null, $timeToLive = 600, $refresh = false) {
    if (is_array($widget_id) && count($widget_id) < 1) {
      return array();
    }

    $query = $this->createQuery('ps')
      ->whereIn('ps.widget_id', $widget_id)
      ->andWhere('status = ' . PetitionSigning::STATUS_COUNTED)
      ->select('MIN(created_at) AS min_created, MAX(created_at) AS max_created');

    $this->dateFilter($query, $min_date, $max_date);

    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    if ($refresh)
      $query->expireResultCache();

    $ret = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW);

    if ($ret) {
      return $ret[0];
    }

    return null;
  }

  public function fetchSigningDateRangeByPetition($petition_id, $min_date = null, $max_date = null, $timeToLive = 600, $refresh = false) {
    $query = $this->createQuery('ps')
      ->whereIn('ps.petition_id', $petition_id)
      ->andWhere('status = ' . PetitionSigning::STATUS_COUNTED)
      ->select('MIN(created_at) AS min_created, MAX(created_at) AS max_created');

    $this->dateFilter($query, $min_date, $max_date);

    if ($timeToLive)
      $query->useResultCache(true, $timeToLive);

    if ($refresh)
      $query->expireResultCache();

    $ret = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW);
    if ($ret) {
      return $ret[0];
    }

    return null;
  }

  private function dateFilter(Doctrine_Query $query, $min_date = null, $max_date = null, $alias = 'ps', $field = 'created_at') {
    if ($min_date > 0) {
      $query->andWhere("$alias.$field >= ?", gmdate('Y-m-d H:i:s', $min_date));
    }

    if ($max_date > 0) {
      $query->andWhere("$alias.$field <= ?", gmdate('Y-m-d H:i:s', $max_date));
    }
  }

  public function queryPendingSignings($duration, $once = true, $petition_id = null) {
    $before = gmdate('Y-m-d H:i:s', time() - $duration);

    $query = $this->createQuery('ps')
      ->where('ps.status = ?', PetitionSigning::STATUS_PENDING);
    if ($once) {
      $query->andWhere('ps.mailed_at IS NULL AND ps.updated_at < ?', array($before));
    } else {
      $query->andWhere('(ps.mailed_at IS NULL AND ps.updated_at < ?) OR (ps.mailed_at IS NOT NULL and ps.mailed_at < ?)', array($before, $before));
    }
    $query->orderBy('ps.status ASC, ps.mailed_at ASC, updated_at ASC, ps.id ASC');

    if ($petition_id) {
      $query->andWhere('ps.petition_id = ?', array($petition_id));
    }

    return $query;
  }

  public function queryPendingSigningsSinceCreation($duration, $petition_id = null) {
    $before = gmdate('Y-m-d H:i:s', time() - $duration);

    $query = $this->createQuery('ps')
      ->where('ps.status = ?', PetitionSigning::STATUS_PENDING)
      ->andWhere('ps.created_at < ?', array($before))
      ->orderBy('ps.status ASC, ps.created_at ASC, ps.id ASC');

    if ($petition_id) {
      $query->andWhere('ps.petition_id = ?', array($petition_id));
    }

    return $query;
  }

  /**
   * @return array
   */
  public function lastSignings($petition_id, $limit = 10, $page = 0, $order = 'date_desc', $name_type = null) {
    $query = $this->createQuery('ps')
      ->where('ps.petition_id = ?', $petition_id)
      ->andWhere('ps.status = ?', PetitionSigning::STATUS_COUNTED)
      ->limit($limit)
      ->offset($limit * $page);

    switch ($order) {
      case 'name_asc':
        if ($name_type === Petition::NAMETYPE_SPLIT) {
          $query->orderBy('ps.lastname ASC, ps.id ASC');
        } else {
          $query->orderBy('ps.fullname ASC, ps.id ASC');
        }
        break;
      case 'name_desc':
        if ($name_type === Petition::NAMETYPE_SPLIT) {
          $query->orderBy('ps.lastname DESC, ps.id DESC');
        } else {
          $query->orderBy('ps.fullname DESC, ps.id DESC');
        }
        break;
      case 'city_asc':
        $query->orderBy('ps.city ASC, ps.id ASC');
        break;
      case 'city_desc':
        $query->orderBy('ps.city DESC, ps.id DESC');
        break;
      case 'country_asc':
        $query->orderBy('ps.country ASC, ps.city ASC, ps.id ASC');
        break;
      case 'country_desc':
        $query->orderBy('ps.country DESC, ps.city DESC, ps.id DESC');
        break;
      case 'date_asc':
        $query->orderBy('ps.updated_at ASC');
        break;
      default:
        $query->orderBy('ps.updated_at DESC');
    }

    return $query->execute();
  }

  /**
   * @return int
   */
  public function lastSigningsTotal($petition_id) {
    return $this->createQuery('ps')
        ->where('ps.petition_id = ?', $petition_id)
        ->andWhere('ps.status = ?', PetitionSigning::STATUS_COUNTED)
        ->count();
  }

  public function updateByDownload(Download $download, $limit = 100000) {
    if (!$download->getId() || !$download->getIncremental() || !$download->getPetition()->getId() || !$download->getUser()->getId()) {
      throw new Exception('error on updateByDownload');
    }

    $field = $download->getSubscriber() ? 'download_subscriber_id' : 'download_data_id';
    $query = $this->createQuery()->update('PetitionSigning ps');
    $query->where("$field IS NULL");
    $query->set($field, $download->getId());
    $query->andWhere('ps.status = ?', PetitionSigning::STATUS_COUNTED);
    $this->andQuerySubscriber($query, null, $download->getSubscriber());
    $query->andWhere('ps.petition_id = ?', $download->getPetition()->getId());
    $query->limit($limit);
    $query->orderBy('ps.id ASC');

    $rows = $query->execute();

    return $rows;
  }

  public function countNewIncrement(Petition $petition, $subscriber = false) {
    return $this->query(array(
          self::PETITION => $petition,
          self::SUBSCRIBER => $subscriber ? true : false,
          self::DOWNLOAD_NULL => $subscriber ? true : false
      ))->count();
  }

  public function countOldIncrement(Download $download) {
    return $this->query(array(
          self::PETITION => $download->getPetition(),
          self::SUBSCRIBER => $download->getSubscriber() ? true : false,
          self::DOWNLOAD => $download
      ))->count();
  }

}
