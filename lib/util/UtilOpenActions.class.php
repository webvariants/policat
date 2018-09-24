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

  const MAX = 10;

  /**
   *
   * @param integer $id
   * @param string $lang
   * @return array
   */

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
      ->leftJoin('p.Campaign c')
      ->andWhere('c.status = ?', CampaignTable::STATUS_ACTIVE)
      ->leftJoin('p.PetitionText pt')
      ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
      ->andWhere('pt.language_id = ?', 'en')
//      ->leftJoin('pt.DefaultWidget w')
//      ->andWhere('w.status = ?', Widget::STATUS_ACTIVE)
      ->select('p.*, pt.*, c.id, c.object_version')
      ->addSelect('(SELECT count(z.id) FROM PetitionSigning z WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z.created_at  and z.petition_id = p.id and z.status = ' . PetitionSigning::STATUS_COUNTED . ') as signings24')
      ->limit(self::MAX * 2);

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

        $target = Petition::calcTarget($count, $petition['target_num']);
        $exerpts[] = array(
            'title' => $title,
            'text' => $shorten,
            'target' => $target,
            'percent' => (int) ($count / $target * 100),
            'signings' => $count,
            'signings24' => $petition->getCleanData('signings24', 0),
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

}
