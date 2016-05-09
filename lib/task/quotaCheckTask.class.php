<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class quotaCheckTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace = 'policat';
    $this->name = 'quota-check';
    $this->briefDescription = 'Check all campaign quotas';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $context = sfContext::createInstance($this->configuration);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    if (StoreTable::value(StoreTable::BILLING_ENABLE)) {
      $campaigns = CampaignTable::getInstance()->queryBillingEnabled()->execute();
      $quota_table = QuotaTable::getInstance();
      foreach ($campaigns as $campaign) {
        /* @var $campaign Campaign */
//        echo $campaign->getName() . "\n";
        $quota_table->activateQuota($campaign, true);
      }
      echo "Campaign quotas updated.\n";
    } else {
      echo "Billing diabled. nothing to do.\n";
    }
  }

}
