<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * api (V2) actions.
 *
 * @package    policat
 * @subpackage api_v2
 * @author     Christoph, Martin
 */
class api_v2Actions extends policatActions {

  /**
   * Executes doc action
   *
   * @param sfRequest $request A request object
   */
  public function executeDoc(sfWebRequest $request) {

  }

  /**
   * Executes actionSignings action
   *
   * @param sfRequest $request A request object
   */
  public function executeActionSignings(sfWebRequest $request) {
    $this->setLayout(false);
    $response = $this->getResponse();
    /* @var $response sfWebResponse */

    // does the client want JSONP?
    $callback = trim(preg_replace('/[^a-z_0-9]/i', '', $request->getParameter('callback', null)));

    // determine the requested action (petition)
    $action_id = $request->getParameter('action_id');
    if (!is_numeric($action_id) || $action_id < 0) {
      $response->setStatusCode(400);
      return $this->renderJson(array('status' => 'error', 'message' => 'bad action ID given'), $callback);
    }

    $petition = PetitionTable::getInstance()->findByIdCachedActive($action_id);
    if (!$petition) {
      $response->setStatusCode(404);
      return $this->renderJson(array('status' => 'error', 'message' => 'action could not be found'), $callback);
    }

    $signings_table = PetitionSigningTable::getInstance();
    $token_table = PetitionApiTokenTable::getInstance();
    $timeToLive = 60;
    $refresh = false;

    $token_code = $request->getPostParameter('token');
    if ($token_code) {
      $token = $token_table->fetchByPetitionAndToken($petition, $token_code, PetitionApiTokenTable::STATUS_ACTIVE);
      if (!$token) {
        return $this->renderJson(array('status' => 'error', 'message' => 'token wrong'), $callback);
      }

      $foreign_singings = $request->getPostParameter('signings', null);
      if ($foreign_singings) {
        if (is_array($foreign_singings)) {
          $offsets = ApiTokenOffsetTable::getInstance()->fetchOffsetsByToken($token);
          $change = false;

          foreach ($foreign_singings as $country_code => $country_addnum) {
            if (preg_match('/^[a-z]{2}$/i', $country_code) && preg_match('/^-?[0-9]+$/i', $country_addnum)) {
              $country_code = strtoupper($country_code);
              if ($offsets->contains($country_code)) {
                $offset = $offsets->get($country_code);
                /* @var $offset ApiTokenOffset */

                if ($offset->getAddnum() != $country_addnum) {
                  $offset->setAddnum($country_addnum);
                  $change = true;
                }
              } else {
                $offset = new ApiTokenOffset();
                $offset->setApiToken($token);
                $offset->setCountry($country_code);
                $offset->setAddnum($country_addnum);
                $change = true;
                $offset->save();
              }
            }
          }

          if ($change) {
            $offsets->save();
            $refresh = true;
          }
        } else {
          return $this->renderJson(array('status' => 'error', 'message' => 'wrong format'), $callback);
        }
      }
    }

    // widget filter: collect desired widget ids (comma separated list)
    $widget_ids = $request->getParameter('widgets', '');

    if ($widget_ids) {
      if ($widget_ids === true || $widget_ids === 'true' || $widget_ids === 'TRUE') {
        $widget_ids = true;
      } else {
        $widget_ids = array_filter(array_unique(array_map('intval', explode(',', $widget_ids))));
      }
    } else {
      $widget_ids = null;
    }

    // prepare date range filter
    $min_date = $request->getParameter('from_via_policat', null);
    $max_date = $request->getParameter('to_via_policat', null);
    $with_date = $min_date !== null || $max_date !== null;

    if ($min_date !== null && $max_date !== null && $min_date > $max_date) {
      $t = $min_date;
      $min_date = $max_date;
      $max_date = $t;
    }

    $min_date = (int) $min_date;
    $max_date = (int) $max_date;

    $segregate = $request->getParameter('segregate');
    $by_countries = $segregate === 'countries';

    $data = array('action_id' => (int) $action_id);

//    // as per customer requests, we only add the addnum stuff when we are NOT filtering by widget(s)
//    if (empty($widgets) && !$countryFilter && !$min_date && !$max_date) {
//      $data['signings'] += $add_num;
//    }

    if ($widget_ids) {
      foreach (WidgetTable::getInstance()->fetchIdsByPetition($petition) as $widget_id) {
        if (($widget_ids === true) || (is_array($widget_ids) && in_array($widget_id, $widget_ids))) {
          if (!array_key_exists('widgets', $data)) {
            $data['widgets'] = array();
            $data['widget_first_signing'] = array();
            $data['widget_last_signing'] = array();
          }

          if ($by_countries) {
            $widget_data = $signings_table->countByWidgetCountries($widget_id, $min_date, $max_date, $timeToLive, $refresh);
          } else {
            $widget_data = $signings_table->countByWidget($widget_id, $min_date, $max_date, $timeToLive, $refresh);
          }
          $data['widgets'][(int) $widget_id] = $widget_data;

          if ($widget_data) {
            $widget_min_max = $signings_table->fetchSigningDateRange(array_keys($data['widgets']), $min_date, $max_date, $timeToLive, $refresh);

            if ($widget_min_max) {
              $data['widget_first_signing'][(int) $widget_id] = strtotime($widget_min_max['min_created']);
              $data['widget_last_signing'][(int) $widget_id] = strtotime($widget_min_max['max_created']);
            }
          }

          $data['widgets_first_signing'] = min($data['widget_first_signing']);
          $data['widgets_last_signing'] = max($data['widget_last_signing']);
        }
      }
    }

    if ($by_countries) {
      $data['signings_via_policat'] = $signings_table->countByPetitionCountries($action_id, $min_date, $max_date, $timeToLive, $refresh);
    } else {
      $data['signings_via_policat'] = $signings_table->countByPetition($action_id, $min_date, $max_date, $timeToLive, $refresh);
    }

    if ($data['signings_via_policat']) {
      $action_min_max = $signings_table->fetchSigningDateRangeByPetition($action_id, $min_date, $max_date, $timeToLive, $refresh);

      if ($action_min_max) {
        $data['policat_first_signing'] = strtotime($action_min_max['min_created']);
        $data['policat_last_signing'] = strtotime($action_min_max['max_created']);
      }
    }

    if ($by_countries) {
      $data['signings_via_api'] = $token_table->sumOffsetsCountry($action_id, $timeToLive, $refresh);
    } else {
      $data['signings_via_api'] = $token_table->sumOffsets($action_id, $timeToLive, $refresh);
    }

    $data['manual_counter_tweak'] = (int) $petition->getAddNum();

    if (!$with_date) {
      if ($by_countries) {
        $total = array();
        foreach ($data['signings_via_policat'] as $country => $num) {
          $total[$country] = $num;
        }

        if (!array_key_exists('unknown', $total)) {
          $total['unknown'] = 0;
        }

        $total['unknown'] += $data['manual_counter_tweak'];

        foreach ($data['signings_via_api'] as $country => $num) {
          if (array_key_exists($country, $total)) {
            $total[$country] += $num;
          } else {
            $total[$country] = $num;
          }
        }
      } else {
        $total = $data['signings_via_api'] + $data['signings_via_policat'] + $data['manual_counter_tweak'];
      }
      $data['signings_total'] = $total;
    }

    $response->setHttpHeader('Cache-Control', null);
    $response->addCacheControlHttpHeader('public');
    $response->addCacheControlHttpHeader('max-age', 60);

    return $this->renderJson($data, $callback);
  }

  /**
   * @param sfRequest $request A request object
   */
  public function executeActionLastSignings(sfWebRequest $request) {
    $this->setLayout(false);
    $response = $this->getResponse();
    /* @var $response sfWebResponse */

    $response->setHttpHeader('Cache-Control', null);

    // does the client want JSONP?
    $callback = trim(preg_replace('/[^a-z_0-9]/i', '', $request->getParameter('callback', null)));

    // determine the requested action (petition)
    $action_id = $request->getParameter('action_id');
    if (!is_numeric($action_id) || $action_id < 0) {
      $response->setStatusCode(400);
      return $this->renderJson(array('status' => 'error', 'message' => 'bad action ID given'), $callback);
    }

    $page = $request->getParameter('page');
    if (!is_numeric($page) || $page < 1) {
      $response->setStatusCode(400);
      return $this->renderJson(array('status' => 'error', 'message' => 'bad page given'), $callback);
    }

    if ($page > 10000) {
      $response->setStatusCode(400);
      return $this->renderJson(array('status' => 'error', 'message' => 'bad page given'), $callback);
    }

    $petition = PetitionTable::getInstance()->findByIdCachedActive($action_id);
    if (!$petition) {
      $response->setStatusCode(404);
      return $this->renderJson(array('status' => 'error', 'message' => 'action could not be found'), $callback);
    }

    if ($petition->getLastSignings() == PetitionTable::LAST_SIGNINGS_NO) {
      $response->setStatusCode(404);
      return $this->renderJson(array('status' => 'error', 'message' => 'disabled for this action'), $callback);
    }

    $response->addCacheControlHttpHeader('public');
    $response->addCacheControlHttpHeader('max-age', 60);

    $order = 'date_desc';
    $route_params = $this->getRoute()->getParameters();
    $type = isset($route_params['type']) ? $route_params['type'] : null;
    $page_size = $type === 'large' ? 500 : 30;

    if ($type === 'list') {
      $order = $request->getParameter('order');
      $page_size = 500;
    }

    $signings = PetitionSigningTable::getInstance()->lastSignings($action_id, $page_size, $page - 1, $order, $petition->getNametype());
    if (!$signings) {
      $response->setStatusCode(404);
      return $this->renderJson(array('status' => 'error', 'message' => 'nothing found'), $callback);
    }

    $data = array(
        'action_id' => (int) $action_id,
        'page' => (int) $page,
        'time' => time(),
        'total' => PetitionSigningTable::getInstance()->lastSigningsTotal($action_id)
    );

    $data['pages'] = ceil($data['total'] / $page_size);

    $with_city = $petition->getLastSigningsCity() && $petition->getWithAddress();
    $with_country = $petition->getLastSigningsCountry() && $petition->getWithCountry();

    $signers = array();
    foreach ($signings as $signing) {
      /* @var $signing PetitionSigning  */

      $entry = array(
          'name' => $signing->getComputedName(),
          'date' => $signing->getCreatedAt()
      );

      if ($with_city) {
        $entry['city'] = $signing->getCity();
      }
      if ($with_country) {
        $entry['country'] = $signing->getCountry();
      }
      $signers[] = $entry;
    }

    $data['status'] = 'ok';
    $data['signers'] = $signers;
    $data['fields'] = array('name', 'date');
    if ($with_city) {
      $data['fields'][] = 'city';
    }
    if ($with_country) {
      $data['fields'][] = 'country';
    }

    $response->addCacheControlHttpHeader('public');
    $response->addCacheControlHttpHeader('max-age', 60);

    return $this->renderJson($data, $callback);
  }

}
