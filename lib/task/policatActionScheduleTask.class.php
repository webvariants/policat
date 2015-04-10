<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatActionScheduleTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        new sfCommandOption('silent', null, sfCommandOption::PARAMETER_OPTIONAL, 'Prevent output', 0),
        new sfCommandOption('utc-hour', null, sfCommandOption::PARAMETER_OPTIONAL, 'Execute only if between xx:00 and xx:59 UTC', ''),
      // add your own options here
    ));

    $this->namespace = 'policat';
    $this->name = 'action-schedule';
    $this->briefDescription = 'Invalides cache intries for actions on day change. Run once at 00:01 each day.';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $table = PetitionTable::getInstance();
    $con = $table->getConnection();
    $silent = $options['silent'];
    $utc_hour = $options['utc-hour'];

    if ($utc_hour !== '') {
      if (gmdate('H') !== $utc_hour) {
        if (!$silent) {
          echo "exiting, it is not the right hour.\n";
        }
        return;
      }
    }

    $petitions = $table->fetchScheduleNeed();
    foreach ($petitions as $petition) {
      /* @var $petition Petition */

      if (!$silent) {
        echo $petition->getId() . "\t" . $petition->getName() . "\n";
      }

//      $petition->state(Doctrine_Record::STATE_DIRTY); // with cachetagging this does not help, we have to change something
      $kind = $petition->getKind();
      $petition->setKind(0);
      $petition->setKind($kind);

      $petition->save();
    }

    if (!$silent) {
      echo "done.\n";
    }
  }

}
