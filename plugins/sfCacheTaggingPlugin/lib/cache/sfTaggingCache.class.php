<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * This code adds opportunity to use cache tagging, there are extra methods to
   * work with cache tags and locks
   *
   * @package sfCacheTaggingPlugin
   * @subpackage cache
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfTaggingCache extends sfCache implements sfTaggingCacheInterface
  {
    /**
     * This cache stores your data any instanceof sfCache
     *
     * @var sfCache
     */
    protected $cache = null;

    /**
     * Log file pointer
     *
     * @var resource
     */
    protected $fileResource = null;


    /**
     * Tag content handler with namespace holder (tag setting/adding/removing)
     *
     * @var sfContentTagHandler
     */
    protected $contentTagHandler = null;


    /**
     * @var sfCacheTagLogger
     */
    protected $logger = null;

    /**
     * Extended verion of default getOption method
     * In case, options is array with sub arrays you could easy to get value
     * by joining array key names with "."
     *
     * @example
     *   # options array:
     *    array(
     *      'php' => array(
     *        'frameworks' => array(
     *          'ZF'  => 'Zend Framework',
     *          'Yii' => 'Yii',
     *          'sf'  => 'Symfony',
     *        ),
     *      ),
     *    );
     *
     * "Symfony" will be accessed by $keyPath "php.frameworks.sf"
     *
     * @param string  $name
     * @param mixed   $default
     * @return mixed
     */
    protected function getArrayValueByKeyPath ($keyPath, $array)
    {
      $dotPosition = strpos($keyPath, '.');

      if (0 < $dotPosition)
      {
        $firstKey = substr($keyPath, 0, $dotPosition);

        if (isset($array[$firstKey]) && is_array($array[$firstKey]))
        {
          $lastKeys = substr($keyPath, $dotPosition + 1);

          return $this->getArrayValueByKeyPath($lastKeys, $array[$firstKey]);
        }
      }

      return isset($array[$keyPath]) ? $array[$keyPath] : null;
    }

    /**
     * Returns option by key path
     *
     * @see PHPDOC of method self::getArrayValueByKeyPath
     * @param string  $name     Array key, or key path joined by "."
     * @param mixed   $default  optional on unsuccess return default value
     * @return mixed
     */
    public function getOption ($name, $default = null)
    {
      $option = $this->getArrayValueByKeyPath($name, $this->options);

      return null === $option ? $default : $option;
    }

    /**
     * Initialization process based on parent but without calling parent method
     *
     * @see sfCache::initialize
     * @throws sfInitializationException
     * @param array $options
     */
    public function initialize ($options = array())
    {
      parent::initialize((array) $options);

      $this->contentTagHandler = new sfContentTagHandler();

      $cacheClassName = $this->getOption('storage.class');

      if (! $cacheClassName)
      {
        throw new sfInitializationException(sprintf(
          'You must pass a "storage.class" option to initialize a %s object.',
          __CLASS__
        ));
      }

      if (! class_exists($cacheClassName, true))
      {
        throw new sfInitializationException(
          sprintf('Data cache class "%s" not found', $cacheClassName)
        );
      }

      # check is valid class
      $this->cache = new $cacheClassName($this->getOption('storage.param', array()));

      if (! $this->cache instanceof sfCache)
      {
        throw new sfInitializationException(
          'Data cache class is not instance of sfCache.'
        );
      }

      if (! $this->getOption('logger.class'))
      {
        throw new sfInitializationException(sprintf(
          'You must pass a "logger.class" option to initialize a %s object.',
          __CLASS__
        ));
      }

      $loggerClassName = $this->getOption('logger.class');

      if (! class_exists($loggerClassName, true))
      {
        throw new sfInitializationException(
          sprintf('Logger cache class "%s" not found', $loggerClassName)
        );
      }

      $this->logger = new $loggerClassName(
        $this->getOption('logger.param', array())
      );

      if (! $this->logger instanceof sfCacheTagLogger)
      {
        throw new sfInitializationException(sprintf(
          'Logger class is not instance of sfCacheTagLogger, got "%s"',
          get_class($this->logger)
        ));
      }
    }

    /**
     * Returns cache class for data caching
     *
     * @return sfCache
     */
    public function getCache ()
    {
      return $this->cache;
    }

    /**
     * @return sfCacheTagLogger
     */
    protected function getLogger ()
    {
      return $this->logger;
    }

    /**
     * @since v1.4.0
     *    parent::has() replaced by $this->get()
     *    build-in has method does not check if cache
     *    is expired (by comparing contents cache tags version)
     *    works little longer and in the same time accurately
     *
     * @see sfCache::get
     * @param string $key
     * @return boolean
     */
    public function has ($key)
    {
      $has = null !== $this->get($key);

      $this->getLogger()->log($has ? 'H' : 'h', $key);

      return $has;
    }

    /**
     * Removes cache from backend by key
     *
     * @see sfCache::remove
     * @param string $key
     * @return boolean
     */
    public function remove ($key)
    {
      $cacheMetadata = new CacheMetadata($this->getCache()->get($key));

      $this->deleteTags($cacheMetadata->getTags());

      $result = $this->getCache()->remove($key);

      $this->getLogger()->log($result ? 'D' : 'd', $key);

      return $result;
    }

    /**
     * @see sfCache::removePattern
     * @param string $pattern
     * @return boolean
     */
    public function removePattern ($pattern)
    {
      return $this->getCache()->removePattern($pattern);
    }

    /**
     * @see sfCache::getTimeout
     * @param string $key
     * @return int
     */
    public function getTimeout ($key)
    {
      return $this->getCache()->getTimeout($key);
    }

    /**
     * Returns "Time To Live" in seconds
     *
     * @param string $key
     * @return integer
     */
    public function getTTL ($key)
    {
      $timeout = $this->getTimeout($key);

      return 0 == $timeout ? $this->getLifetime(null) : ($timeout - time());
    }

    /**
     * @see sfCache::getLastModified
     * @param string $key
     * @return int
     */
    public function getLastModified ($key)
    {
      return $this->getCache()->getLastModified($key);
    }

    /**
     * Adds tags to existring data cache
     * Useful, when tags are generated after data is cached
     * (i.g. doctrine object cache)
     *
     * If appending tag already exists, we will compare version to save
     * tag with newest one
     *
     * @param string  $key
     * @param array   $tags
     * @return boolean
     */
    public function addTagsToCache ($key, array $tags)
    {
      $cacheMetadata = new CacheMetadata($this->getCache()->get($key));

      $data = $cacheMetadata->getData();

      if (null === $data)
      {
        return false;
      }

      $cacheMetadata->addTags($tags);

      $tags = $cacheMetadata->getTags();

      return $this->set($key, $data, $this->getTTL($key), $tags);
    }

    /**
     * Sets data into the cache with related tags
     *
     * @see sfCache::set
     * @param string  $key
     * @param mixed   $data
     * @param integer $timeout optional
     * @param array   $tags    optional
     * @return mixed  false - when cache expired/not valid
     *                true  - in other case
     */
    public function set ($key, $data, $timeout = null, array $tags = array())
    {
      $result = false;

      if (! $this->isLocked($key))
      {
        $this->lock($key);

        $result = $this->getCache()->set(
          $key, array('data' => $data, 'tags' => $tags), $timeout
        );

        $this->getLogger()->log($result ? 'S' : 's', $key);

        $this->setTags($tags);

        $this->unlock($key);
      }
      else
      {
        $this->getLogger()->log('s', $key);
      }

      return $result;
    }

    /**
     * Saves tag with its version
     *
     * @param string    $key      tag name
     * @param string    $tagVersion   tag version
     * @param integer   $lifetime     optional tag time to live
     * @return boolean
     */
    public function setTag ($key, $tagVersion, $lifetime = null)
    {
      $result = $this->getCache()->set($key, $tagVersion, $lifetime);

      $this->getLogger()->log($result ? 'P' :'p', sprintf('%s(%s)', $key, $tagVersion));

      return $result;
    }

    /**
     * Saves tags with its version
     *
     * @param array    $tags
     * @param integer  $lifetime optional
     */
    public function setTags (array $tags, $lifetime = null)
    {
      foreach ($tags as $tagName => $version)
      {
        $this->setTag($tagName, $version, $lifetime);
      }
    }

    /**
     * Returns version of the tag by key
     *
     * @param string $key
     * @return string version of the tag
     */
    public function getTag ($key)
    {
      $result = $this->getCache()->get($key);

      $this->getLogger()->log(
        $result ? 'T' : 't',
        $key . ($result ? "({$result})" : '')
      );

      return $result;
    }

    /**
     * Checks tag key exists
     *
     * @param string $key
     * @return boolean
     */
    public function hasTag ($key)
    {
      $has = $this->getCache()->has($key);

      $this->getLogger()->log($has ? 'I' : 'i', $key);

      return $has;
    }

    /**
     * Returns associated cache tags
     *
     * @param string $key
     * @return array
     */
    public function getTags ($key)
    {
      $cacheMetadata = new CacheMetadata($this->getCache()->get($key));

      return $cacheMetadata->getTags();
    }

    /**
     * Removes tag version (basicly called on physical object removing)
     *
     * @param string $key
     * @return boolean
     */
    public function deleteTag ($key)
    {
      $result = $this->getCache()->remove($key);

      $this->getLogger()->log($result ? 'E' : 'e', $key);

      return $result;
    }

    /**
     * Deletes tags
     *
     * @param array $tags
     * @return array
     */
    public function deleteTags (array $tags)
    {
      $deletions = array();

      foreach ($tags as $name => $version)
      {
        $deletions[$name] = $this->deleteTag($name) ? 1 : 0;
      }

      return $deletions;
    }

    /**
     * Invalidate tags
     *
     * @param array $tags
     * @return null
     */
    public function invalidateTags (array $tags)
    {
      foreach ($tags as $name => $version)
      {
        $this->setTag($name, sfCacheTaggingToolkit::generateVersion());
      }
    }

    /**
     * Pulls data out of cache.
     * Also, it checks all related tags for expiration/version-up.
     *
     * @see sfCache::get
     * @param string  $key
     * @param mixed   $default returned back if result is false
     * @return mixed
     */
    public function get ($key, $default = null)
    {
      $cacheMetadata = new CacheMetadata(
        $this->getCache()->get($key, $default)
      );

      $data = $cacheMetadata->getData();

      if (null !== $data)
      {
        $fetchedCacheTags = $cacheMetadata->getTags();

        if (0 !== count($fetchedCacheTags))
        {
          /**
           * speed up multi tag selection from backend
           */
          $tagKeys = array_keys($fetchedCacheTags);

          $storedTags = $this->getCache()->getMany($tagKeys);

          $hasExpired = false;

          /**
           * getMany returns keys with NULL value if some key is missing.
           * In case arrays are equal, cache is not expired
           */
          if ($fetchedCacheTags === $storedTags)
          {
            $this->getLogger()->log('V', 'via equal compare');

            # one tag is expired, no reasons to continue
            # (should revalidate cache data)
          }
          else
          {
            $extendedKeysWithCurrentVersions = array_combine(array_keys($storedTags), array_values($fetchedCacheTags));

            # check for data tags is expired
            foreach ($storedTags as $tagKey => $tagLatestVersion)
            {
              $tagVersion = $extendedKeysWithCurrentVersions[$tagKey];
              # tag is exprired or version is old
              if (! $tagLatestVersion || $tagVersion < $tagLatestVersion)
              {
                $this->getLogger()->log(
                  'v', sprintf('%s(%s=>%s)', $tagKey, $tagVersion, $tagLatestVersion)
                );

                # one tag is expired, no reasons to continue
                # (should revalidate cache data)
                $hasExpired = true;

                break;
              }

              $this->getLogger()->log(
                'V', sprintf('%s(%s)', $tagKey, $tagLatestVersion)
              );
            }
          }

          // some cache tags is invalidated
          if ($hasExpired)
          {
            if ($this->isLocked($key))
            {
              # return old cache coz new data is writing to the current cache
              $data = $cacheMetadata->getData();
            }
            else
            {
              # cache no locked, but cache is expired
              $data = null;
            }
          }
          else
          {
            $data = $cacheMetadata->getData();
          }
        }
        else
        {
          $data = $cacheMetadata->getData();
        }
      }

      $this->getLogger()->log($data !== $default ? 'G' : 'g', $key);

      return $data;
    }

    /**
     * Set lock on $key on $expire seconds
     *
     * @param string    $lockName
     * @param integer   $expire expire time in seconds
     * @return boolean true: was locked
     *                 false: could not lock
     */
    public function lock ($lockName, $expire = 2)
    {
      $key = $this->generateLockKey($lockName);

      $result = $this->getCache()->set($key, 1, $expire);

      $this->getLogger()->log($result ? 'L' : 'l', $key);

      return $result;
    }

    /**
     * Check for $lockName is locked/not locked
     *
     * @param string $lockName
     * @return boolean
     */
    public function isLocked ($lockName)
    {
      $key = $this->generateLockKey($lockName);

      $result = $this->getCache()->has($key);

      $this->getLogger()->log($result ? 'R' : 'r', $key);

      return $result;
    }

    /**
     * Call this to unlock key
     *
     * @param string $lockName
     * @return boolean
     */
    public function unlock ($lockName)
    {
      $key = $this->generateLockKey($lockName);

      $result = $this->getCache()->remove($key);

      $this->getLogger()->log($result ? 'U' : 'u', $lockName);

      return $result;
    }

    /**
     * @see sfCache::clean
     * @param integer  $mode   One of sfCache::ALL, sfCache::OLD params
     * @return null
     */
    public function clean ($mode = sfCache::ALL)
    {
      $this->getCache()->clean($mode);
    }

    /**
     * Creates name for lock key
     *
     * @param string $key
     * @return string
     */
    protected function generateLockKey ($key)
    {
      return "{$key}_lock";
    }

    /**
     * Retrieves handler to manage tags
     *
     * @return sfContentTagHandler
     */
    public function getContentTagHandler ()
    {
      return $this->contentTagHandler;
    }

    /**
     * @return array registered keys in storage
     */
    public function getCacheKeys ()
    {
      return $this->getCache()->getCacheKeys();
    }
  }
