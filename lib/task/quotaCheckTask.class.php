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
        new sfCommandOption('verbose', 'v', sfCommandOption::PARAMETER_REQUIRED, 'be verbose', 0)
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
    $subscription = StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_ENABLE);
    $extra_days = (int) StoreTable::value(StoreTable::BILLING_SUBSCRIPTION_EXTRA_DAYS);

    if (StoreTable::value(StoreTable::BILLING_ENABLE)) {
      $campaigns = CampaignTable::getInstance()->queryBillingEnabled()->execute();
      $quota_table = QuotaTable::getInstance();
      foreach ($campaigns as $campaign) {
        /* @var $campaign Campaign */
        if ($options['verbose']) {
          echo $campaign->getName() . "\n";
        }
        $active_quota = $quota_table->activateQuota($campaign, true);
        if ($subscription && $active_quota) {
          if ($active_quota->subscriptionRenewPossible()) {
            if ($options['verbose']) {
              echo '  renew order' . "\n";

            }

            $order = new Order();
            $last_order = $active_quota->getOrder();
            $order->fillByOrder($last_order);
            $quota = new Quota();
            $quota->copyProduct($active_quota->getProduct());
            $quota->setUser($active_quota->getUser());
            $quota->setCampaign($active_quota->getCampaign());
            $quota->setOrder($order);
            $active_quota->setRenewOfferred(1);
            if ($extra_days > 0) {
                $active_quota->setEndAt(gmdate('Y-m-d H:i:s', strtotime($active_quota->getEndAt()) + $extra_days * 24 * 60 * 60));
            }
            $active_quota->save();

            $quota->save();
            $campaign->setOrder($order);
            $campaign->save();

            $ticket = TicketTable::getInstance()->generate(array(
                TicketTable::CREATE_CAMPAIGN => $quota->getCampaign(),
                TicketTable::CREATE_KIND => TicketTable::KIND_SUBSCRIPTION_ORDER_ISSUED,
                TicketTable::CREATE_CHECK_DUPLICATE => false
            ));
            if ($ticket) {
                $ticket->save();
                $ticket->notifyAdmin();
            }
          }
        }
      }
      echo "Campaign quotas updated.\n";
    } else {
      echo "Billing diabled. nothing to do.\n";
    }
  }

}
