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
 * @author     Martin
 */
class api_v2_incommingActions extends policatActions {

  /**
   * Bounce
   *
   * @param sfRequest $request A request object
   */
  public function executeBounce(sfWebRequest $request) {
    $response = $this->getResponse();
    if ($response instanceof sfWebResponse) {
      $response->addCacheControlHttpHeader('no-cache');
    }

    $this->checkAuthToken();

    $time = time();
    $data = $this->getJsonRequestData(false);

    $config = $this->getBounceConfig();
    if (!$config || !$data) {
      return $this->renderJson(array('status' => 'no data'));
    }

    if ($config['grouping']) {
      foreach ($data as $data_i) {
        if (is_array($data_i)) {
          $this->handleBounceData($data_i, $config, $time);
        }
      }
    } else {
      $this->handleBounceData($data, $config, $time);
    }

    return $this->renderJson(array('status' => 'ok'));
  }

  private function handleBounceData($data, $config, $time) {
    if ($config['match'] && is_array($config['match'])) {
      foreach ($config['match'] as $key => $value) {
        if (!array_key_exists($key, $data) || $data[$key] !== $value) {
          return false;
        }
      }
    }

    $emailField = $config['email'];
    if (!$emailField) {
      return false;
    }

    $email = array_key_exists($emailField, $data) && is_string($data[$emailField]) ? $data[$emailField] : null;
    $id = $config['id'] && array_key_exists($config['id'], $data) && is_string($data[$config['id']]) ? $data[$config['id']] : null;
    $campaign = $config['campaign'] && array_key_exists($config['campaign'], $data) && is_string($data[$config['campaign']]) ? $data[$config['campaign']] : null;
    $blocked = $config['blocked'] && array_key_exists($config['blocked'], $data) && is_scalar($data[$config['blocked']]) ? $data[$config['blocked']] : null;
    $hard_bounce = $config['hard_bounce'] && array_key_exists($config['hard_bounce'], $data) && is_scalar($data[$config['hard_bounce']]) ? $data[$config['hard_bounce']] : null;
    $error_related_to = $config['error_related_to'] && array_key_exists($config['error_related_to'], $data) && is_scalar($data[$config['error_related_to']]) ? $data[$config['error_related_to']] : null;
    $error = $config['error'] && array_key_exists($config['error'], $data) && is_scalar($data[$config['error']]) ? $data[$config['error']] : null;

    if ($config['log_file']) {
      $dir = sfConfig::get('sf_log_dir');
      file_put_contents($dir . '/' . $config['log_file'], date('Y-m-d H:i:s') . " $email, id: $id, camapign: $campaign, blocked: $blocked, hard: $hard_bounce, related: $error_related_to, error: $error\n", FILE_APPEND);
    }

    $campaign_prefix = $config['campaign_prefix'];
    if ($campaign_prefix) {
      if (!$campaign || strpos($campaign, $campaign_prefix . '-') !== 0) {
        return false;
      }

      $campaign = substr($campaign, strlen($campaign_prefix) + 1);
    }

    $this->parse($id, $idKey, $idNumber);
    switch ($idKey) {
      case 'Signing':
        $table = PetitionSigningTable::getInstance();
        $signing = $table->findOneById($idNumber);
        $con = $table->getConnection();
        /* @var $signing PetitionSigning */
        if ($signing && $signing->getId()) {
          if ($hard_bounce && ($signing->getVerified() != PetitionSigning::VERIFIED_YES) && StoreTable::getInstance()->findByKeyCached(StoreTable::EMAIL_DELETE_HARD_BOUCNE_IMMEDIATELY)) {
            $signing->delete();
            $con->exec('update petition set deleted_hard_bounces = deleted_hard_bounces + 1 where id = ?', array($signing->getPetitionId()));
          } else {
            $signing->setBounce(1);
            $signing->setBounceAt(gmdate('Y-m-d H:i:s', $time));
            $signing->setBounceBlocked($blocked ? 1 : 0);
            $signing->setBounceHard($hard_bounce ? 1 : 0);
            $signing->setBounceRelatedTo($error_related_to);
            $signing->setBounceError($error);
            $signing->save();
          }
        }

        break;
    }

    switch ($campaign) {
      case 'Testmail':
        $store_entry = StoreTable::getInstance()->findByKey(StoreTable::INTERNAL_LAST_TESTING_BOUNCE, true);
        $store_entry->setValue(date('Y-m-d H:i:s') . " $email, blocked: $blocked, hard: $hard_bounce, related: $error_related_to, error: $error");
        $store_entry->save();
        break;
    }

    return true;
  }

  private function parse($subject, &$key, &$number) {
    if (preg_match('/^([a-zA-Z-]+)-(\d+)$/', (string) $subject, $matches)) {
      $key = $matches[1];
      $number = $matches[2];
    } else {
      $key = null;
      $number = null;
    }
  }

  private function checkAuthToken() {
    $token = $this->getToken();
    if (!$token) {
      $this->forward403('Missing auth config.');
    }

    $auth_user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER'] : null;
    $auth_password = array_key_exists('PHP_AUTH_PW', $_SERVER) ? $_SERVER['PHP_AUTH_PW'] : null;

    if ($auth_user !== 'token' || !hash_equals($token, $auth_password)) {
      $this->forward403();
    }
  }

  private function getToken() {
    $api = sfConfig::get('app_mail_api');
    if (!is_array($api) || !array_key_exists('token', $api) || !is_string($api['token'])) {
      return false;
    }

    return $api['token'];
  }

  private function getBounceConfig() {
    $api = sfConfig::get('app_mail_api');
    if (!is_array($api) || !array_key_exists('bounce', $api) || !is_array($api['bounce'])) {
      return false;
    }

    $bounce = $api['bounce'];

    foreach (array('match', 'token', 'grouping', 'log_file', 'email', 'id', 'campaign', 'campaign_prefix', 'blocked', 'hard_bounce', 'error_related_to', 'error') as $field) {
      if (!array_key_exists($field, $bounce)) {
        $bounce[$field] = null;
      }
    }

    return $bounce;
  }

}
