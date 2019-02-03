<?php
/*
 * Copyright (c) 2019, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class printI18NTask extends sfBaseTask {

  protected function configure() {
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'widget'),
        new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
        new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace = 'policat';
    $this->name = 'print-i18n';
    $this->briefDescription = 'print I18N countries';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array()) {
    $cultures = sfCultureInfo::getCultures();
    foreach ($cultures as $culture) {
        try {
            $c = sfCultureInfo::getInstance($culture);
        } catch (Exception $e) {
            continue;
        }
        echo "\n";
        echo $culture . "\t" . $c->getEnglishName() . "\t" .$c->getNativeName() . "\n";
        $countries =  $c->getCountries();
        foreach ($countries as $iso => $country) {
          echo "\t$iso\t$country\n";
        }
    }
  }

}
