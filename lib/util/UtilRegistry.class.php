<?php

class UtilRegistry {

  static function get($key, $default = null, $class = false) {
    $tagger = sfContext::getInstance()->getViewCacheManager()->getTagger();
    if ($tagger instanceof sfTagCache) {
      $cache_key = 'registry_' . $key;
      $value = $tagger->get($cache_key);

      if (is_array($value)) {
        if ($class)
          return $value;
        else
          return reset($value);
      }
    }

    $reg = Doctrine_Core::getTable('Registry')
        ->createQuery('r')
        ->where('r.regkey = ?', $key)
        ->fetchOne();

    if (!$reg)
      return $default;

    if ($tagger instanceof sfTagCache)
      $tagger->set($cache_key, array($reg['value'], $reg['regclass']), null, array($reg->getTagName() => $reg->getObjectVersion()));

    if ($class)
      return array($reg['value'], $reg['regclass']);
    else
      return $reg['value'];
  }

  static function set($key, $value, $class = '') {
    $con = Doctrine_Core::getConnectionByTableName('Registry');
    $con->beginTransaction();
    try {
      $reg = Doctrine_Core::getTable('Registry')
          ->createQuery('r')
          ->where('r.regkey = ?', $key)
          ->fetchOne();

      if (!$reg) {
        $reg = new Registry();
        $reg->setRegkey($key);
      }

      $reg->setRegclass($class);
      $reg->setValue($value);
      $reg->save();
      $con->commit();

      return true;
    } catch (Exception $e) {
      $con->rollback();
    }

    return false;
  }

}