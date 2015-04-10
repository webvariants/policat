<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatEmailHashWorkerTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        new sfCommandOption('loop', null, sfCommandOption::PARAMETER_OPTIONAL, 'Loop batch until all signings hashed', ''),
        new sfCommandOption('maxload', null, sfCommandOption::PARAMETER_OPTIONAL, 'Max unix load inside loop', 0.6),
      // add your own options here
    ));

    $this->namespace = 'policat';
    $this->name = 'email-hash-worker';
    $this->briefDescription = 'Calculate hashes for signings';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);

    if ($options['loop']) {
      $max_load = (float) $options['maxload'];
      echo "maxload: $max_load\n";
      do {
        $lastline = system('php symfony policat:email-hash-worker');
        echo PHP_EOL;
        $load = sys_getloadavg();
        $maxwait = 250;
        $waited = 0;
        while ($load[0] > $max_load && $maxwait--) {
          sleep(1);
          $waited++;
          $load = sys_getloadavg();
        }
        if ($waited) {
          echo "waited $waited s\n";
        }
        usleep(500000); // 0.5s
      } while (strpos($lastline, 'nothing to do') === false);
    } else {

      // initialize the database connection
      $databaseManager = new sfDatabaseManager($this->configuration);
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
      $table = PetitionSigningTable::getInstance();
      $con = $table->getConnection();

      $petition_signings = $table
        ->createQuery('ps')
        ->select('ps.id, ps.email')
        ->where('ps.status = ? and ps.email_hash IS NULL', PetitionSigning::STATUS_VERIFIED)
        ->limit(100)
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW);

      $count = count($petition_signings);

      if ($count) {
        echo "processing $count signings...";
        foreach ($petition_signings as $row) {
          $con->exec('UPDATE petition_signing SET email_hash = ? WHERE id = ?', array(UtilEmailHash::hash($row['email']), $row['id']));
        }

        echo " done.";
      } else {
        echo "nothing to do.";
      }
    }
  }

}
