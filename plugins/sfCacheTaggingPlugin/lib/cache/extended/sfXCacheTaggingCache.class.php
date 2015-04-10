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
  class sfXCacheTaggingCache extends sfXCacheCache
    implements sfTaggingCacheInterface
  {
    /**
     * @return array
     */
    public function getCacheKeys ()
    {
      $this->checkAuth();

      $keys = array();
      for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++)
      {
        $infos = xcache_list(XC_TYPE_VAR, $i);

        if (is_array($infos['cache_list']))
        {
          foreach ($infos['cache_list'] as $info)
          {
            $keys[] = substr($info['name'], strlen($this->getOption('prefix')));
          }
        }
      }

      return $keys;
    }
  }