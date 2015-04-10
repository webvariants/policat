<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class StoreForm extends BaseForm {

  public function setup() {
    $meta = $this->getOption('meta');

    foreach ($meta['fields'] as $key => $field) {
      list($class, $options, $attribues) = self::getTriple($field['widget']);
      $widget = new $class($options, $attribues);
      if ($widget instanceof WidgetLogo && $this->getStoreValue($key))
        $widget->setOption('file_src', $widget->getOption('file_src') . '/' . $this->getStoreValue($key) . '?' . $this->getStoreVersion($key));
      $this->setWidget($key, $widget);
      if ($this->getStoreValue($key))
        $this->getWidgetSchema()->setDefault($key, $this->getStoreValue($key));

      if (array_key_exists('help', $field)) {
        $this->getWidgetSchema()->setHelp($key, $field['help']);
      }

      list($class, $options, $messages) = self::getTriple($field['validator']);
      $this->setValidator($key, new $class($options, $messages));
    }

    $this->widgetSchema->setNameFormat('store[%s]');
    $this->getWidgetSchema()->setFormFormatterName('bootstrap');
  }

  protected $store_cache = array();

  protected function saveStoreCache() {
    $con = StoreTable::getInstance()->getConnection();
    $con->beginTransaction();
    try {
      foreach ($this->store_cache as $store)
        $store->save();
      $con->commit();
    } catch (Exception $e) {
      $con->rollback();
      throw new $e;
    }
  }

  /**
   *
   * @return Store
   */
  protected function getStore($key) {
    if (array_key_exists($key, $this->store_cache))
      return $this->store_cache[$key];

    $meta = $this->getOption('meta');
    $store = isset($meta['i18n']) ? StoreTable::getInstance()->findByKeyAndLanguage($key, $this->getOption('language_id')) : StoreTable::getInstance()->findByKey($key);
    if ($store)
      return $this->store_cache[$key] = $store;

    $this->store_cache[$key] = new Store();
    $this->store_cache[$key]->setKey($key);
    if (isset($meta['i18n'])) {
      $this->store_cache[$key]->setLanguageId($this->getOption('language_id'));
    }
    return $this->store_cache[$key];
  }

  protected function getStoreValue($key) {
    $meta = $this->getOption('meta');
    if (isset($meta['json']))
      return $this->getStore($meta['json'])->getField($key);
    else
      return $this->getStore($key)->getValue();
  }

  protected function getStoreVersion($key) {
    $meta = $this->getOption('meta');
    if (isset($meta['json']))
      return $this->getStore($meta['json'])->getObjectVersion();
    else
      return $this->getStore($key)->getObjectVersion();
  }

  protected function setStoreValue($key, $value) {
    $meta = $this->getOption('meta');
    if (isset($meta['json']))
      $this->getStore($meta['json'])->setField($key, $value);
    else
      $this->getStore($key)->setValue($value);
  }

  protected static function getTriple($array) {
    $c = count($array);
    return array(
        $array[0],
        $c > 1 ? $array[1] : array(),
        $c > 2 ? $array[2] : array()
    );
  }

  public function save() {

    $meta = $this->getOption('meta');
    foreach ($meta['fields'] as $key => $field) {
      $value = $this->getValue($key);
      if (array_key_exists('file', $field) && $field['file']) {
        if ($value instanceof sfValidatedFile) {
          if ($value->getTempName()) {
            $filename = $key . $value->getExtension();
            $value->save($filename);
            if ($this->getStoreValue($key) && $this->getStoreValue($key) != $filename)
              @unlink($value->getPath() . '/' . $this->getStoreValue($key));
            $this->setStoreValue($key, $filename);
          }
        }
      }
      else
        $this->setStoreValue($key, $value);
    }
    $this->saveStoreCache();
  }

}
