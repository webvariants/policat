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
  class CacheMetadata
  {
    /**
     * @var sfParameterHolder
     */
    protected $holder = null;

    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * @param mixed $data
     * @param array $tags
     */
    public function __construct ($metadata = null)
    {
      $this->holder = new sfParameterHolder();

      if (! is_array($metadata))
      {
        return;
      }

      $data = isset($metadata['data']) ? $metadata['data'] : null;
      $tags = isset($metadata['tags']) ? $metadata['tags'] : array();

      $this->initialize($data, $tags);
    }

    /**
     * @param mixed $data
     * @param array $tags
     * @return null
     */
    public function initialize ($data, array $tags = array())
    {
      $this->setTags($tags);
      $this->setData($data);
    }

    /**
     * @return sfParameterHolder
     */
    protected function getHolder ()
    {
      return $this->holder;
    }

    /**
     * @param mixed $data
     * @return null
     */
    public function setData ($data)
    {
      $this->data = $data;
    }

    /**
     * @return array
     */
    public function getTags ()
    {
      return $this->getHolder()->getAll();
    }

    /**
     * Rewrites all existing tags with new
     *
     * @param array $tags
     * @return null
     */
    public function setTags (array $tags)
    {
      $this->getHolder()->clear();
      $this->getHolder()->add($tags);
    }

    /**
     * Return cache data (content)
     *
     * @return mixed
     */
    public function getData ()
    {
      return $this->data;
    }

    /**
     * Checks for tag exists
     *
     * @param string $tagName
     * @return boolean
     */
    public function hasTag ($tagName)
    {
      return $this->getHolder()->has($tagName);
    }

    /**
     * Appends tags to existing
     *
     * @param array $tags
     * @return null
     */
    public function addTags (array $tags)
    {
      foreach ($tags as $name => $version)
      {
        $this->setTag($name, $version);
      }
    }

    /**
     * @param string $tagName
     * @return false|string
     */
    public function getTag ($tagName)
    {
      return $this->getHolder()->get($tagName);
    }

    /**
     * @param string $tagName
     * @param string $tagVersion
     * @return null
     */
    public function setTag ($tagName, $tagVersion)
    {
      $has = $this->hasTag($tagName);

      if (! $has || ($has && $this->getTag($tagName) < $tagVersion))
      {
        $this->getHolder()->set($tagName, $tagVersion);
      }
    }
  }