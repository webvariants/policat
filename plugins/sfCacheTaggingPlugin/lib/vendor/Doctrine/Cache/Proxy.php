<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * Class to replace doctrine cache engine with symfony's cache engine
   * This is only for storing cache with its associated tags
   * (Doctrine does not yet supports ability to add tags on stored cache)
   *
   * @package sfCacheTaggingPlugin
   * @subpackage doctrine
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class Doctrine_Cache_Proxy extends Doctrine_Cache_Driver
  {
    /**
     * Short method to retrieve sfTaggingCache for internal use
     *
     * @throws sfCacheDisabledException when some mandatory objects are missing
     * @return sfTaggingCache
     */
    protected function getTaggingCache ()
    {
      return sfCacheTaggingToolkit::getTaggingCache();
    }

    /**
     * @see parent::_doSave()
     * @return boolean
     */
    protected function _doSave ($id, $data, $ttl = false)
    {
      try
      {
        return $this->getTaggingCache()->set($id, $data, ! $ttl ? null : $ttl);
      }
      catch (sfCacheDisabledException $e)
      {
        sfCacheTaggingToolkit::notifyApplicationLog(
          __CLASS__, $e->getMessage(), sfLogger::NOTICE
        );
      }

      return false;
    }

    /**
     * @see parent::_doSave()
     * @return boolean
     */
    protected function _doSaveWithTags ($id, $data, $ttl, $tags)
    {
      try
      {
        return $this
          ->getTaggingCache()
          ->set($id, $data, ! $ttl ? null : $ttl, $tags);
      }
      catch (sfCacheDisabledException $e)
      {
        sfCacheTaggingToolkit::notifyApplicationLog(
          __CLASS__, $e->getMessage(), sfLogger::NOTICE
        );
      }

      return false;
    }

    /**
     * @return array
     */
    protected function _getCacheKeys ()
    {
      try
      {
        return $this->getTaggingCache()->getCacheKeys();
      }
      catch (sfCacheDisabledException $e)
      {
        sfCacheTaggingToolkit::notifyApplicationLog(
          __CLASS__, $e->getMessage(), sfLogger::NOTICE
        );
      }

      return;
    }

    /**
     * @see parent::_doDelete()
     * @return boolean
     */
    protected function _doDelete ($id)
    {
      try
      {
        return $this->getTaggingCache()->remove($id);
      }
      catch (sfCacheDisabledException $e)
      {
        sfCacheTaggingToolkit::notifyApplicationLog(
          __CLASS__, $e->getMessage(), sfLogger::NOTICE
        );
      }

      return false;
    }

    /**
     * @see parent::_doContains()
     * @return boolean
     */
    protected function _doContains ($id)
    {
      try
      {
        return $this->getTaggingCache()->has($id);
      }
      catch (sfCacheDisabledException $e)
      {
        sfCacheTaggingToolkit::notifyApplicationLog(
          __CLASS__, $e->getMessage(), sfLogger::NOTICE
        );
      }

      return false;
    }

    /**
     * @see parent::_doFetch()
     * @return mixed
     */
    protected function _doFetch ($id, $testCacheValidity = true)
    {
      try
      {
        $value = $this->getTaggingCache()->get($id);

        return null === $value ? false : $value;
      }
      catch (sfCacheDisabledException $e)
      {
        sfCacheTaggingToolkit::notifyApplicationLog(
          __CLASS__, $e->getMessage(), sfLogger::NOTICE
        );
      }

      return false;
    }

    /**
     * Saves cache with its tags
     *
     * @param string  $id
     * @param string  $data
     * @param int     $ttl    Time To Live
     * @param array   $tags
     * @return boolean
     */
    public function saveWithTags ($id, $data, $ttl = false, array $tags = array())
    {
      return $this->_doSaveWithTags($this->_getKey($id), $data, $ttl, $tags);
    }
  }