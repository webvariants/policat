<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class testMailTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        new sfCommandOption('silent', null, sfCommandOption::PARAMETER_OPTIONAL, 'Prevent output', 0),
        new sfCommandOption('to', null, sfCommandOption::PARAMETER_REQUIRED, 'Email-Address', false),
        new sfCommandOption('subject', null, sfCommandOption::PARAMETER_OPTIONAL, 'Subject', 'Monitoring Policat'),
    ));

    $this->namespace = 'policat';
    $this->name = 'test-mail';
    $this->briefDescription = 'Send test mail for monitoring';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    sfContext::createInstance($this->configuration);

    $silent = $options['silent'];

    $body = "Monitoring mail\nDate: " . date(DATE_RFC3339) . "\n\nBye.\n";

    UtilMail::send(null, null, $options['to'], $options['subject'], $body);

    if (!$silent) {
      echo "done.\n";
    }
  }

}
