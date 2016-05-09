<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

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