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
  class sfAPCTaggingCache extends sfAPCCache
    implements sfTaggingCacheInterface
  {
    public function getCacheKeys ()
    {
      // apc_cache_info returns false, if APC is disabled
      // no reasons to check it - all methods available after
      // calling self::__construct
      $infos = apc_cache_info('user');

      $keys = array();

      foreach ($infos['cache_list'] as $info)
      {
        $keys[] = substr($info['info'], strlen($this->getOption('prefix')));
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

      $values = apc_fetch($keys);

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