<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * sfCacheTaggingPlugin configuration
   *
   * @package sfCacheTaggingPlugin
   * @subpackage config
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfCacheTaggingPluginConfiguration extends sfPluginConfiguration
  {
    /**
     * Handy method to get type hinting in IDE's
     *
     * @return sfEventDispatcher
     */
    public function getEventDispatcher ()
    {
      return $this->dispatcher;
    }

    public function initialize ()
    {
      $manager = Doctrine_Manager::getInstance();

      # collection with additional methods to with tags
      $manager->setAttribute(
        Doctrine::ATTR_COLLECTION_CLASS, 'Doctrine_Collection_Cachetaggable'
      );

      $manager->setAttribute(
        Doctrine_Core::ATTR_RESULT_CACHE, new Doctrine_Cache_Proxy()
      );

      $manager->setAttribute(
        Doctrine_Core::ATTR_QUERY_CLASS, 'Doctrine_Query_Cachetaggable'
      );

      $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);

      $this->getEventDispatcher()->notify(
        new sfEvent($manager, 'sf_cache_tagging_plugin.doctrine_configure')
      );

      $this->getEventDispatcher()->connect(
        'component.method_not_found',
        array('sfCacheTaggingToolkit', 'listenOnComponentMethodNotFoundEvent')
      );
    }
  }
