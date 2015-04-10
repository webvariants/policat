<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * Cache class that stores content sqlite database
   * This class differs from parent with set() and get() methods
   * Added opportunity to store objects via serialization/unserialization
   *
   * @package sfCacheTaggingPlugin
   * @subpackage cache
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfSQLiteTaggingCache extends sfSQLiteCache
    implements sfTaggingCacheInterface
  {
    /**
     * @see sfSQLiteCache::get()
     */
    public function get ($key, $default = null)
    {
      $data = parent::get($key, $default);

      return null === $data ? $default : unserialize($data);
    }

    /**
     * @see sfSQLiteCache::set()
     */
    public function set ($key, $data, $lifetime = null)
    {
      return parent::set($key, serialize($data), $lifetime);
    }

    /**
     * @return array
     */
    public function getCacheKeys ()
    {
      $rows = $this->dbh->arrayQuery(
        sprintf('SELECT key FROM cache WHERE timeout > %d', time()),
        SQLITE_ASSOC
      );

      $keys = array();
      foreach ($rows as $row)
      {
        $keys[] = $row['key'];
      }

      return $keys;
    }

    /**
     * @see sfCache
     * @param array $keys
     * @return array
     */
    public function getMany ($keys)
    {
      $rows = parent::getMany($keys);

      foreach ($rows as $key => $value)
      {
        $rows[$key] = unserialize($value);
      }

      return $rows;
    }
  }