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
  class sfEAcceleratorTaggingCache extends sfEAcceleratorCache
    implements sfTaggingCacheInterface
  {
    /**
     * @return array
     */
    public function getCacheKeys ()
    {
      $infos = eaccelerator_list_keys();

      if (! is_array($infos))
      {
        return null;
      }

      $keys = array();

      foreach ($infos as $info)
      {
        $keys[] = substr($info['name'], strlen($this->getOption('prefix')));
      }

      return $keys;
    }
  }