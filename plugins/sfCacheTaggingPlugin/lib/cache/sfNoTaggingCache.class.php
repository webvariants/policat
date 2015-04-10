<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * This pseudo sfTaggingCache class serve to keep application working
   * if you have disabled "cache" in settings.yml
   *
   * @package sfCacheTaggingPlugin
   * @subpackage cache
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfNoTaggingCache extends sfTaggingCache
  {
    /**
     * @see sfTaggingCache
     */
    public function initialize ($options = array())
    {
      $this->contentTagHandler = new sfContentTagHandler();
    }

    /**
     * @see sfTaggingCache
     */
    public function setTags (array $tags, $lifetime = null)
    {
      return true;
    }

    /**
     * @see sfTaggingCache
     */
    public function hasTag ($tagKey)
    {
      return false;
    }

    /**
     * @see sfTaggingCache
     */
    public function addTagsToCache ($key, array $tags)
    {
      return true;
    }

    /**
     * @see sfTaggingCache
     */
    public function setTag ($tagKey, $tagValue, $lifetime = null)
    {
      return true;
    }

    /**
     * @see sfTaggingCache
     */
    public function getTag ($tagKey)
    {
      return false;
    }

    /**
     * @see sfTaggingCache
     */
    public function getTags ($key)
    {
      return array();
    }

    /**
     * @see sfTaggingCache
     */
    public function deleteTag ($tagKey)
    {
      return true;
    }

    /**
     * @see sfCache
     */
    public function get ($key, $default = null)
    {
      return $default;
    }

    /**
     * @see sfCache
     */
    public function has ($key)
    {
      return false;
    }

    /**
     * @see sfCache
     */
    public function set ($key, $data, $lifetime = null)
    {
      return true;
    }

    /**
     * @see sfCache
     */
    public function remove ($key)
    {
      return true;
    }

    /**
     * @see sfCache
     */
    public function removePattern ($pattern)
    {
      return true;
    }

    /**
     * @see sfCache
     */
    public function clean ($mode = self::ALL)
    {
      return true;
    }

    /**
     * @see sfCache
     */
    public function getLastModified ($key)
    {
      return 0;
    }

    /**
     * @see sfCache
     */
    public function getTimeout ($key)
    {
      return 0;
    }

    /**
     * @see sfTaggingCache
     *
     * @return sfNoCache
     */
    public function getDataCache ()
    {
      return new sfNoCache();
    }

    /**
     * @see sfTaggingCache
     *
     * @return sfNoCache
     */
    public function getTagsCache ()
    {
      return new sfNoCache();
    }
  }