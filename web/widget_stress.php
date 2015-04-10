<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('widget', 'stress', false);
sfContext::createInstance($configuration)->dispatch();
