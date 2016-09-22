<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatSendEmailsTask extends sfBaseTask {

  /**
   * @see sfTask
   */
  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'frontend'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('message-limit', null, sfCommandOption::PARAMETER_OPTIONAL, 'The maximum number of messages to send', 100),
        new sfCommandOption('time-limit', null, sfCommandOption::PARAMETER_OPTIONAL, 'The time limit for sending messages (in seconds)', 5),
        new sfCommandOption('repeats', null, sfCommandOption::PARAMETER_OPTIONAL, 'Repeat sending n-times to get continous mail flow.', 11),
    ));

    $this->namespace = 'policat';
    $this->name = 'send-emails';

    $this->briefDescription = 'Sends emails stored in a queue';

    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    new sfDatabaseManager($this->configuration);

    $spool = $this->getMailer()->getSpool();
    $spool->setMessageLimit($options['message-limit']);
    $spool->setTimeLimit($options['time-limit']);

    $start_time = time();

    $last_time = $start_time + $options['time-limit'] * ($options['repeats'] - 1);
    $lock_time = $start_time + $options['time-limit'] * $options['repeats'];
    if (!$this->lock($lock_time)) {
      return;
    }

    $i = 0;
    $sum = 0;
    do {
      $i++;
      $sent = $this->getMailer()->flushQueue();
      $sum += $sent;
      if ($sent) {
        $this->logSection('emails', sprintf('[%s] sent %s emails', $i, $sent));
      }
      $time = time();
      $diff = $time - $start_time;

      $wait = ($options['time-limit'] - $diff) % $options['time-limit'];
      if ($wait === 0) {
        $wait = $options['time-limit'];
      }

      if ($wait + $time <= $last_time) {
        sleep($wait);
      } else {
        break;
      }
    } while (time() <= $last_time);

    if (!$sum) {
      $this->logSection('emails', sprintf('No emails sent.'));
    }

    $this->lock(false);
  }

  private function lock($until) {
    $lockFile = sfConfig::get('sf_data_dir') . '/send-emails.lck';

    if ($until) {
      if (file_exists($lockFile)) {
        $time = (int) file_get_contents($lockFile);
        if (time() < $time) {
          $this->logSection('emails', sprintf('can not get lock (locked until %s)', $time));
          return false;
        }
      }
      return file_put_contents($lockFile, $until);
    } else {
      if (file_exists($lockFile)) {
        unlink($lockFile);
      }

      return null;
    }
  }

}
