<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * @package sfCacheTaggingPlugin
   * @subpackage cache
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfMemcacheTaggingCache extends sfMemcacheCache
    implements sfTaggingCacheInterface
  {
    public function getCacheKeys ()
    {
      $keys = array();

      foreach ($this->getCacheInfo() as $key)
      {
        $keys[] = substr($key, strlen($this->getOption('prefix')));
      }

      return $keys;
    }

    /**
     * @see sfCache
     * @return array
     */
    public function getMany ($keys)
    {
      $prefix = $this->getOption('prefix');

      foreach ($keys as $i => $key)
      {
        $keys[$i] = $prefix . $key;
      }

      $values = $this->getBackend()->get($keys);

      $results = array();

      $prefixLength = strlen($prefix);

      foreach ($keys as $i => $key)
      {
        $shortKey = substr($key, $prefixLength);
        $results[$shortKey] = isset($values[$key]) ? $values[$key] : null;
      }

      return $results;
    }
  }