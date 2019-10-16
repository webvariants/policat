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

  protected function queryPendingSignings(Petition $petition) {
    return PetitionSigningTable::getInstance()->query([
      PetitionSigningTable::PETITION => $petition->getId(),
      PetitionSigningTable::MAILEXPORT_PENDING => PetitionSigning::MAILEXPORT_PENDING_YES,
      PetitionSigningTable::SUBSCRIBER => true
    ]);
  }

  abstract public function formSetup(Petition $petition, MailExportSettingForm $form);

  abstract public function getName();

  abstract public function export(Petition $petition, $verbose = false);
}