<?php

abstract class MailExport {

  const SERVICES = ['Rapidmail'];

  public static $services = [];

  public static function getService($name) {
    if (!in_array($name, self::SERVICES)) {
      return null;
    }

    if (!array_key_exists($name, self::$services)) {
      $class = 'MailExport' . ucfirst($name);
      self::$services[$name] = new $class;
    }

    return self::$services[$name];
  }

  public static function getServices() {
    foreach (self::SERVICES as $name) {
      yield $name => self::getService($name);
    }
  }

  public static function checkOneEnabled(Petition $petition) {
    foreach (self::getServices() as $service) {
      if ($service->checkEnabled($petition)) {
        return true;
      }
    }

    return false;
  }

  abstract public function formSetup(MailExportSettingForm $form);

  abstract public function getName();
}