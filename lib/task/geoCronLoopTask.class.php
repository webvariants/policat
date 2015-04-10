<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class geoCronLoopTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'policat';
    $this->name             = 'geo-cron-loop';
    $this->briefDescription = 'Send emails from Geo Activism Loop until all sent';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array())
  {
    $max = 3;
    do {
      $lastline = system('php symfony policat:geo-cron');
      echo PHP_EOL;
    } while (strpos($lastline, 'continue') && $max--);
  }
}
