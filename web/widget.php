<?php

if (array_key_exists('xhprof', $_GET)) {
  $ini = parse_ini_file(__DIR__ . '/../config/properties.ini');
  if (array_key_exists('xhprof', $ini) && $ini['xhprof'] && $ini['xhprof'] === $_GET['xhprof']) {

    include_once __DIR__ . '/../lib/util/UtilXhprof.class.php';
    UtilXhprof::start();
  }
}

require_once(__DIR__ . '/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('widget', 'prod', true);
sfContext::createInstance($configuration)->dispatch();
