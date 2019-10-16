<?php
/*
 * Copyright (c) 2019, webvariants GmbH, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class openEciFetchCounterTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        new sfCommandOption('verbose', 'v', sfCommandOption::PARAMETER_REQUIRED, 'be verbose', 0)
    ));

    $this->namespace = 'policat';
    $this->name = 'openeci-fetch-counter';
    $this->briefDescription = 'Export pending emails contacts to external services.';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $petitions = PetitionTable::getInstance()->queryAll()
      ->andWhere('p.kind = ?', Petition::KIND_OPENECI)
      ->andWhere('p.status = ?', Petition::STATUS_ACTIVE)
      ->execute();
    foreach ($petitions as $petition) {
      /* @var $petition Petition */
      if ($options['verbose']) {
        echo "fetch petition: " . $petition->getId() .  "\n";
      }

      if (!$petition->getOpeneciUrl() || !$petition->getOpeneciChannel()) {
        if ($options['verbose']) {
          echo "skip\n";
          continue;
        }
      }

      $total = $this->total($petition, $options['verbose']);
      if ($total === null) {
        echo "\n";
        continue;
      }

      if ($options['verbose']) {
        echo "total: " . $total .  "\n";
      }

      $countries = $this->countries($petition, $options['verbose']);
      if ($countries === null) {
        echo "\n";
        continue;
      }

      $countries_json = json_encode($countries);
      if ($options['verbose']) {
        echo $countries_json .  "\n";
      }

      if ($petition->getOpeneciCounterTotal() != $total || $petition->getOpeneciCounterCountries() != $countries_json) {
        $petition->setOpeneciCounterTotal($total);
        $petition->setOpeneciCounterCountries($countries_json);
        $petition->save();
      } else {
        if ($options['verbose']) {
          echo "unchanged\n";
        }
      }
    }

  }

  private function total(Petition $petition, $verbose) {
    $ch = curl_init(rtrim($petition->getOpeneciUrl(), '/') . '/api/total');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'policat');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json, */*',
      'Content-Type: application/json'
    ]);

    $result=curl_exec($ch);

    if ($result === false) {
      $msg = curl_error($ch);
      @curl_close($ch);
      if ($verbose) {
        echo 'connection error';
      }
      return null;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $array = json_decode($result, true);
    if (!is_array($array)) {
      if ($verbose) {
        echo 'unexpected result';
      }
      return null;
    }

    if ($http_code != 200) {
      if ($verbose) {
        echo 'unexpected status (' . $http_code . ')';
      }
      return null;
    }

    if (!array_key_exists('signature', $array)) {
      if ($verbose) {
        echo 'unexpected result';
      }
      return null;
    }

    return $array['signature'];
  }

  private function countries(Petition $petition, $verbose) {
    $ch = curl_init(rtrim($petition->getOpeneciUrl(), '/') . '/api/country');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'policat');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json, */*',
      'Content-Type: application/json'
    ]);

    $result=curl_exec($ch);

    if ($result === false) {
      $msg = curl_error($ch);
      @curl_close($ch);
      if ($verbose) {
        echo 'connection error';
      }
      return null;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $array = json_decode($result, true);
    if (!is_array($array)) {
      if ($verbose) {
        echo 'unexpected result';
      }
      return null;
    }

    if ($http_code != 200) {
      if ($verbose) {
        echo 'unexpected status (' . $http_code . ')';
      }
      return null;
    }

    $result = [];

    foreach ($array as $entry) {
      if (!array_key_exists('country', $entry)
        || !array_key_exists('signature', $entry)
        || !is_string($entry['country'])
        || strlen($entry['country']) !== 2
        || !is_numeric($entry['signature'])) {
        if ($verbose) {
          echo 'unexpected result';
        }
        return null;
      }

      $result[strtoupper($entry['country'])] = (int) $entry['signature'];
    }

    return $result;
  }

}
