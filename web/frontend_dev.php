<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
//if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '127.0.12.1', '::1')))
//{
//  die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
//}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
