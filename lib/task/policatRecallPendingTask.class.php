<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatRecallPendingTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        new sfCommandOption('silent', null, sfCommandOption::PARAMETER_OPTIONAL, 'Prevent output', 0),
        new sfCommandOption('duration', null, sfCommandOption::PARAMETER_OPTIONAL, 'Duration in seconds', 3 * 24 * 60 * 60),
        new sfCommandOption('once', null, sfCommandOption::PARAMETER_OPTIONAL, 'Only if no reminder yet.', 1),
        new sfCommandOption('limit', null, sfCommandOption::PARAMETER_OPTIONAL, 'Limit', 1000),
        new sfCommandOption('action_id', null, sfCommandOption::PARAMETER_OPTIONAL, 'action_id', null),
    ));

    $this->namespace = 'policat';
    $this->name = 'recall-pending';
    $this->briefDescription = 'Send validation e-mails again';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $table = PetitionSigningTable::getInstance();
    $con = $table->getConnection();
    $silent = $options['silent'];
    $duration = (int) $options['duration'];
    $once = (bool) $options['once'];
    $limit = (int) $options['limit'];
    $petition_id = (int) $options['action_id'];
    $time = gmdate('Y-m-d H:i:s');

    $signings = $table->queryPendingSignings($duration, $once, $petition_id)->limit($limit)->execute();
    foreach ($signings as $signing) {
      /* @var $signing PetitionSigning */

      if (!$silent) {
        echo $signing->getId() . "\t" . $signing->getEmail() . "\n";
      }

      UtilEmailValidation::send($signing, 'Reminder:');
      $signing->setMailedAt($time);

//      $signing->save();
    }

    $signings->save();

    if (!$silent) {
      echo "done.\n";
    }
  }

}
