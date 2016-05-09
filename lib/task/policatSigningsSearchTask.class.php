<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatSigningsSearchTask extends sfBaseTask {

  protected function configure() {

    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        new sfCommandOption('min', null, sfCommandOption::PARAMETER_OPTIONAL, 'min', null),
        new sfCommandOption('max', null, sfCommandOption::PARAMETER_OPTIONAL, 'max', null),
        new sfCommandOption('spawn', null, sfCommandOption::PARAMETER_OPTIONAL, 'spawn', null)
    ));

    $this->namespace = 'policat';
    $this->name = 'signings-search';
    $this->briefDescription = 'Update search index table';
    $this->detailedDescription = <<<EOF
The [policat:import-signings|INFO] task does things.
Call it with:

  [php symfony policat:signings-search|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array()) {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $signings_table = PetitionSigningTable::getInstance();
    $search_table = PetitionSigningSearchTable::getInstance();

    $q = $signings_table->createQuery('s')->select('min(s.id)');
    if ($options['min']) {
      $q->andWhere('s.id >= ?', $options['min']);
    }
    $min = $q->fetchArray();
    $min = reset($min);
    $min = reset($min);

    $q = $signings_table->createQuery('s')->select('max(s.id)');
    if ($options['max']) {
      $q->andWhere('s.id <= ?', $options['max']);
    }
    $max = $q->fetchArray();
    $max = reset($max);
    $max = reset($max);

    printf("min: %s\n", $min);
    printf("max: %s\n", $max);

    $pos = $min;

    if ($options['spawn']) {
      $step = 10000;
      while ($pos <= $max) {
        echo 'CALL ' . $pos . ' ' . ($pos + $step) . PHP_EOL;
        $lastline = system('php symfony policat:signings-search --min=' . $pos . ' --max=' . ($pos + $step));
        echo $lastline . PHP_EOL;
        $pos += $step;
      }

      return;
    }

    $step = 1000;

    while ($pos <= $max) {
      $query = $signings_table->createQuery('s')->where('s.id >= ? and s.id < ?', array($pos, $pos + $step));
      $signings = $query->execute();
      printf("step: %s - %s\n", $pos, $pos + $step - 1);
      
      foreach ($signings as $signing) {
        /* @var $signing PetitionSigning */
//        printf("s: %s\n", $signing->getId());
        $search_table->savePetitionSigning($signing);
        $signing->free();
      }

      $query->free();
      $pos += $step;
    }
  }

}
