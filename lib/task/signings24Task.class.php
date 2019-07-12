<?php
/*
 * Copyright (c) 2019, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class signings24Task extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace = 'policat';
    $this->name = 'signings24';
    $this->briefDescription = 'Calculate number of singings for last 24 hours.';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $connection->exec('update petition p set cron_signings24 = (SELECT count(z.id) FROM petition_signing z WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z.created_at and z.petition_id = p.id and z.status = ' . PetitionSigning::STATUS_COUNTED . ')');

    $connection->exec('update widget w set cron_signings24 = (SELECT count(z.id) FROM petition_signing z WHERE DATE_SUB(NOW(),INTERVAL 1 DAY) <= z.created_at and z.widget_id = w.id and z.status = ' . PetitionSigning::STATUS_COUNTED . ')');
  }

}
