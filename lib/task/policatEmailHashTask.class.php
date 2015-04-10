<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatEmailHashTask extends sfBaseTask {

  protected function configure() {

    $this->addArguments(array(
      new sfCommandArgument('email', sfCommandArgument::REQUIRED, 'the email address')
    ));

    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'policat';
    $this->name = 'email-hash';
    $this->briefDescription = 'Generate Email-Hash';
    $this->detailedDescription = <<<EOF
The [policat:email-hash|INFO] task does things.
Call it with:

  [php symfony policat:email-hash|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array()) {
    $email = $arguments['email'];

    printf("TEST: %s\n", UtilEmailHash::test() ? 'ok' : 'FAILED!');
    printf("%s: %s\n", $email, UtilEmailHash::hash($email));
  }

}
