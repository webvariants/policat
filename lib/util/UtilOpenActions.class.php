<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilOpenActions {

  const HOTTEST = 'hottest'; // Trending
  const LARGEST = 'largest'; // Popular
  const RECENT = 'recent'; // New

  const MAX = 15;

  /**
   *
   * @param integer $id
   * @param string $lang
   * @return array
   */

  static private $_day_sql = null;

  static private function daySql() {
    if (self::$_day_sql === null) {
      self::$_day_sql = gmdate('Y-m-d');
    }
    return self::$_day_sql;
  }

  static private function topWidgetByPetition($id, $lang = 'en') {
    $widgets = WidgetTable::getInstance()
      ->createQuery('w')
      ->where('w.petition_id = ?', $id)
      ->leftJoin('w.PetitionText pt')
      ->andWhere('pt.language_id = ?', $lang)
      ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
      ->select('w.*')
      ->addSelect('(SELECT count(s.id) FROM PetitionSigning s WHERE s.widget_id = w.id and s.status = ' . PetitionSigning::STATUS_COUNTED . ') as signings')
      ->orderBy('signings DESC, w.id ASC')
      ->limit(1)
      ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    if ($widgets) {
      return $widgets[0];
    } else {
      return false;
    }
  }

  static private function getPetitions($type) {
    $query = PetitionTable::getInstance()
      ->createQuery('p')
      ->where('p.status = ?', Petition::STATUS_ACTIVE)
      ->andWhere('p.homepage = 1')
      ->andWhere('p.start_at IS NULL OR p.start_at <= ?', self::daySql())
      ->andWhere('(p.end_at IS NULL OR p.end_at > ?)', self::daySql())
      ->leftJoin('p.Campaign c')
      ->andWhere('c.status = ?', CampaignTable::STATUS_ACTIVE)
      ->leftJoin('p.PetitionText pt')
      ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
      ->andWhere('pt.language_id = ?', 'en')
      ->andWhere('5 <= (SELECT count(smin.id) FROM PetitionSigning smin WHERE smin.petition_id = p.id and smin.status = ' . PetitionSigning::STATUS_COUNTED . ')')
      ->select('p.*, pt.*, c.id, c.object_version')
      ->addSelect('(SELECT count(z.id) FROM PetitionSigning z WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z.created_at  and z.petition_id = p.id and z.status = ' . PetitionSigning::STATUS_COUNTED . ') as signings24')
      ->limit(self::MAX * 2);

    if (StoreTable::value(StoreTable::BILLING_ENABLE)) {
      $query->andWhere('(c.billing_enabled = 0 OR c.quota_id IS NOT NULL)');
    }

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
      $query->execute();
  }

  static public function calc() {
    $context = sfContext::getInstance();
    $context->getConfiguration()->loadHelpers('Text');
    $routing = $context->getRouting();

    $tags = array('policat' => '');
    $styles = array();
    $open = array();

    // Open petitions
    foreach (array(self::HOTTEST => 'Trending', self::LARGEST => 'Popular', self::RECENT => 'New') as $key => $value) {
      $data = self::getPetitions($key);
      $exerpts = array();
      $tags[$key] = '';
      foreach ($data as $k => &$petition) {
        if (count($exerpts) >= self::MAX) {
          break;
        }

        if ($key == self::HOTTEST && $petition->getCleanData('signings24', 0) < 1) {
          unset($data[$k]);
          continue;
        }

        if ($key == self::LARGEST && $petition->getCleanData('signings', 0) < 10) {
          unset($data[$k]);
          continue;
        }

        $count = $petition->getCount(60);

        if ($count < 1) {
          continue;
        }

        $number = $count;
        $target = Petition::calcTarget($count, $petition['target_num']);

        $counter_type = ($petition->getKind() == Petition::KIND_EMAIL_TO_LIST && $petition->getShowEmailCounter() == Petition::SHOW_EMAIL_COUNTER_YES) ? 'emails' : 'participants';
        $counter_value = $counter_type === 'emails' ? $petition->countMailsSent() + $petition->getAddnumEmailCounter() : $count;

        if ($petition['kind'] == Petition::KIND_EMAIL_TO_LIST && $petition->getShowEmailCounter() == Petition::SHOW_EMAIL_COUNTER_YES) {
            $number = $petition->countMailsSent() + $petition->getAddnumEmailCounter();
            $target = Petition::calcTarget($number, $petition->getTargetNumEmailCounter());
        }

        $text = $petition['PetitionText'][0];
//        $widget = $text['DefaultWidget'];
        $widget = self::topWidgetByPetition($petition['id']);
        if (!$widget) {
          continue;
        }

        $style = json_decode($widget['stylings'], true);
        $campaign_version = $petition['Campaign']['object_version'];

        $ttags = trim($petition['twitter_tags']);
        if ($ttags) {
          $tags[$key] .= ($tags[$key] ? ' OR ' : '') . $ttags;
        }

        if (!isset($styles[$widget['id']])) {
          $styles[$widget['id']] = array(
              'width' => $style['width'],
              'body_color' => '#818286',
              'target' => $number . '-' . $target,
              'url' => $routing->generate('sign', array('id' => $widget['id'], 'hash' => Widget::calcLastHash(
                    $widget['id'], array(
                      $petition['object_version'],
                      $widget['object_version'],
                      $text['object_version'],
                      $campaign_version
                  ))), true)
          );
        }

        if (in_array($petition['kind'], Petition::$EMAIL_KINDS, false)) {
          $body = Util::enc($widget['email_subject'] ? $widget['email_subject'] : $text['email_subject']) . ', ';
          $body .= Util::enc($widget['email_body'] ? $widget['email_body'] : $text['email_body']);
        } else {
          $body = UtilMarkdown::transform(($widget['intro'] ? $widget['intro'] . " \n\n" : '') . $text['body']);
        }

        $title = $widget['title'] ? $widget['title'] : $text['title'];
        $len = 240 - mb_strlen($title, 'UTF-8');
        $shorten = $len > 20 ? truncate_text(preg_replace('/#[A-Z-]+#/', '', strip_tags($body)), $len) : '';

        $target = Petition::calcTarget($counter_value,  $counter_type === 'emails' ? $petition->getTargetNumEmailCounter() : $petition['target_num']);
        $exerpts[] = array(
            'title' => $title,
            'text' => $shorten,
            'counter_type' => $counter_type,
            'counter_value' => $counter_value,
            'counter_percent' => (int) ($counter_value / $target * 100),
            'key_visual' => $petition['key_visual'],
            'widget_id' => $widget['id'],
            'read_more_url' => $petition['read_more_url'],
            'petition_id' => $petition['id'],
            'kind' => $petition['kind'],
            'widget_last_hash' => Widget::calcLastHash(
              $widget['id'], array(
                $petition['object_version'],
                $widget['object_version'],
                $text['object_version'],
                $campaign_version
            ))
        );
      }

      if (count($exerpts)) {
        $open[$key] = array(
            'title' => $value,
            'excerpts' => $exerpts
        );
      }
    }

    return array(
        'open' => $open,
        'tags' => $tags,
        'styles' => $styles
    );
  }

  public static function cron() {
    $data = self::calc();
    $store_entry = StoreTable::getInstance()->findByKey(StoreTable::INTERNAL_CACHE_OPEN_ACTIONS, true);
    $store_entry->setValue(base64_encode(json_encode($data)));
    $store_entry->save();
  }

  public static function dataByCache() {
    $empty = array(
        'open' => array(),
        'tags' => array(),
        'styles' => array()
    );

    $store_entry = StoreTable::getInstance()->findByKey(StoreTable::INTERNAL_CACHE_OPEN_ACTIONS);
    if (!$store_entry) {
      return $empty;
    }

    $encoded = $store_entry->getValue();
    if (!$encoded) {
      return $empty;
    }

    return json_decode(base64_decode($encoded), true);
  }

  public static function dataChunks($include_types = [], $exclude_petition_ids = []) {
    $openActions = UtilOpenActions::dataByCache();
    $joined = array();
    $petition_ids = array();
    foreach ($openActions['open'] as $type => $tab) {
      if ($include_types && !in_array($type, $include_types)) {
        continue;
      }
      $fiveEach = 5;
      foreach ($tab['excerpts'] as $action) {
        if (!in_array($action['petition_id'], $petition_ids) && (!$exclude_petition_ids || !in_array($action['petition_id'], $exclude_petition_ids))) {
          $joined[] = $action;
          $petition_ids[] = $action['petition_id'];
          $fiveEach--;
          if ($fiveEach === 0) {
              break;
          }
        }
      }
    }
    foreach ($openActions['open'] as $type => $tab) {
      if ($include_types && !in_array($type, $include_types)) {
        continue;
      }
      foreach ($tab['excerpts'] as $action) {
        if (!in_array($action['petition_id'], $petition_ids) && (!$exclude_petition_ids || !in_array($action['petition_id'], $exclude_petition_ids))) {
          $joined[] = $action;
          $petition_ids[] = $action['petition_id'];
        }
      }
    }

    if (count($joined) > 3) {
      array_splice($joined, count($joined) - count($joined) % 3);
    }
    return [array_chunk($joined, 3), $openActions['styles']];
  }

  public static function render($include_types = [], $exclude_petition_ids = []) {
    list($actionListChunk, $styles) = self::dataChunks($include_types, $exclude_petition_ids);
    ?>
    <script type="text/javascript">/*<!--*/
    <?php
    echo UtilWidget::getInitJS();
    foreach ($styles as $widget_id => $stylings) {
      echo UtilWidget::getAddStyleJS($widget_id, $stylings);
    }
    ?>
    //-->
    </script>
    <div style="word-break: break-word;">
        <?php foreach ($actionListChunk as $chunk): ?>
          <div class="card-deck">
              <?php foreach ($chunk as $action): ?>
                <div class="card mb-4" onclick="<?php echo UtilWidget::getWidgetHereJs($action['widget_id'], true) ?>" style="cursor: pointer">
                    <?php if ($action['key_visual']): ?><img style="width: 100%" class="card-img-top img-fluid" src="<?php echo image_path('keyvisual/' . $action['key_visual']) ?>" alt="" /><?php endif ?>
                    <div class="card-body">
                        <p class="mb-1 p-color-less-important"><?php echo Petition::$KIND_SHOW_FE[$action['kind']] ?></p>
                        <div class="progress mb-1">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $action['counter_percent'] ?>%;" aria-valuenow="<?php echo $action['counter_percent'] ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($action['counter_value'], 0, '.', ',') ?></div>
                        </div>
                        <dl class="p-participants text-center mb-0">
                            <dd><?php echo number_format($action['counter_value'], 0, '.', ',') ?></dd>
                            <dt><?php echo $action['counter_type'] ?></dt>
                        </dl>
                        <?php if ($action['title']): ?>
                          <h4 class="mt-1"><?php echo $action['title'] ?></h4>
                        <?php endif ?>
                        <p class="mb-0"><?php echo $action['text'] ?></p>
                    </div>
                    <div class="card-footer text-center">
                        <a class="btn btn-secondary d-block">Take action</a>
                    </div>
                </div>
              <?php endforeach ?>
          </div>
        <?php endforeach ?>
    </div>
    <?php
  }

}
