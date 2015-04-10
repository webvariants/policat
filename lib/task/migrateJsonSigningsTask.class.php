<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class migrateJsonSigningsTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        new sfCommandOption('loop', null, sfCommandOption::PARAMETER_OPTIONAL, 'Loop batch until all signings migrated', ''),
        new sfCommandOption('maxload', null, sfCommandOption::PARAMETER_OPTIONAL, 'Max unix load inside loop', 0.6),
      // add your own options here
    ));

    $this->namespace = 'policat';
    $this->name = 'migrate-json-signings';
    $this->briefDescription = 'Migrate old signings with json encoded data';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);

    if ($options['loop']) {
      $max_load = (float) $options['maxload'];
      echo "maxload: $max_load\n";
      do {
        $lastline = system('php symfony policat:migrate-json-signings');
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
        ->select('ps.id, ps.fields')
        ->where('ps.fields IS NOT NULL AND ps.fields != ""')
        ->limit(1000)
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY_SHALLOW);

      $count = count($petition_signings);

      $valid_fields = array('fullname', 'title', 'firstname', 'lastname', 'address', 'city', 'post_code', 'comment', 'privacy', 'email_subject', 'email_body', 'ref');

      if ($count) {
        echo "processing $count signings...";
        foreach ($petition_signings as $row) {
          $fields = json_decode($row['fields'], true);
          if (!is_array($fields)) {
            die("invalid json: " . $row['fields']);
          }

          $sets = array();
          $values = array();

          foreach ($fields as $key => $value) {
            if (in_array($key, $valid_fields)) {
              $sets[] = "$key = ?";
              $values[] = $value;
            } else {
              if ($key !== 'subscribe') {
                die("invalid key" . $key  . "\n");
              }
            }
          }

          if (!$sets) {
            die("no fields\n");
          }

          $sets[] = 'fields = ?';
          $values[] = null;

          $values[] = $row['id'];
          $update = 'UPDATE petition_signing SET ' . implode(', ', $sets) . ' WHERE id = ?';

//          var_dump(array($update, $values));
          $con->exec($update, $values);
        }

        echo " done.";
      } else {
        echo "nothing to do.";
      }
    }
  }

}
