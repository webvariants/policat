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
  class sfSQLitePDOTaggingCache extends sfSQLitePDOCache
    implements sfTaggingCacheInterface
  {
    /**
     * @see sfSQLitePDOCache::get()
     */
    public function get ($key, $default = null)
    {
      $data = parent::get($key, $default);

      return null === $data ? $default : unserialize($data);
    }

    /**
     * @see sfSQLitePDOCache::set()
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
      $query = 'SELECT key FROM cache WHERE timeout > ?';

      $stmt = $this->getBackend()->prepare($query);

      $stmt->bindValue(1, time(), PDO::PARAM_INT);

      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * @see sfCache
     * @param array $keys
     * @return array
     */
    public function getMany ($keys)
    {
      $list = '';
      foreach ($keys as $key)
      {
        $list .= ($list != '' ? ', ' : '') . $this->getBackend()->quote($key);
      }

      if ($list == '')
      {
        return array();
      }

      $query = "SELECT key, data FROM cache WHERE key IN ({$list}) AND timeout > ?";

      $stmt = $this->getBackend()->prepare($query);

      $stmt->bindValue(1, time(), PDO::PARAM_INT);

      $stmt->execute();

      $rows = array();

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
      {
        $rows[$row['key']] = unserialize($row['data']);
      }

      # reorder based on passed keys
      $results = array();
      foreach ($keys as $key)
      {
        $results[$key] = isset($rows[$key]) ? $rows[$key] : null;
      }

      return $results;
    }
  }