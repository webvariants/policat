<?php

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
