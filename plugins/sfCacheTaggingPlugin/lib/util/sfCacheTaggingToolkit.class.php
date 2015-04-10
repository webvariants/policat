<?php

  /*
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * Toolkit with frequently used methods.
   *
   * @package sfCacheTaggingPlugin
   * @subpackage util
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfCacheTaggingToolkit
  {
    const TEMPLATE_NAME = 'Cachetaggable';

    /**
     * @throws sfCacheDisabledException         when "sf_cache" is OFF
     * @throws sfCacheMissingContextException   if context is not initialized
     * @throws sfConfigurationException         on plugin configuration issues
     * @return sfTaggingCache
     */
    public static function getTaggingCache ()
    {
      if (! sfConfig::get('sf_cache'))
      {
        throw new sfCacheDisabledException('Cache "sf_cache" is disabled');
      }

      if (! sfContext::hasInstance())
      {
        throw new sfCacheMissingContextException(
          sprintf('Content is not initialized for "%s"', __CLASS__)
        );
      }

      $viewCacheManager = sfContext::getInstance()->getViewCacheManager();

      if (! $viewCacheManager instanceof sfViewCacheTagManager)
      {
        throw new sfConfigurationException(
          'sfCacheTaggingPlugin is not properly configured'
        );
      }

      return $viewCacheManager->getTaggingCache();
    }

    /**
     * Build version base on currenct microtime
     *
     * @param double|int $microtime
     * @return string Number list the represents a current timestamp
     */
    public static function generateVersion ($microtime = null)
    {
      $microtime = null === $microtime ? microtime(true) : $microtime;

      return sprintf("%0.0f", pow(10, self::getPrecision()) * $microtime);
    }

    /**
     * Returns app.yml precision, otherwise, return default value (5)
     *
     * @return int
     */
    public static function getPrecision ()
    {
      $presision = (int) sfConfig::get(
        'app_sfCacheTagging_microtime_precision', 5
      );

      if (0 > $presision || 6 < $presision)
      {
        throw new OutOfRangeException(sprintf(
          'Value of "app_sfCacheTagging_microtime_precision" is ' .
            'out of the range (0…6)'
        ));
      }

      return $presision;
    }

    /**
     *
     * @return string
     */
    public static function getModelTagNameSeparator ()
    {
      return (string) sfConfig::get(
        'app_sfCacheTagging_model_tag_name_separator', sfCache::SEPARATOR
      );
    }

    /**
     * Format passed tags to the array
     *
     * @param mixed $argument   false|array|Doctrine_Collection_Cachetaggable|
     *                          Doctrine_Record|ArrayIterator|Doctrine_Table
     *                          IteratorAggregate|Iterator
     * @throws InvalidArgumentException
     * @return array
     */
    public static function formatTags ($argument)
    {
      $tagsToReturn = null;

      if (false === $argument)
      {
        $tagsToReturn = array();
      }
      elseif ($argument instanceof Doctrine_Table)
      {
        $name = sfCacheTaggingToolkit::obtainCollectionName($argument);
        $version = sfCacheTaggingToolkit::obtainCollectionVersion($name);

        $tagsToReturn = array($name => $version);
      }
      elseif (is_array($argument))
      {
        $tagsToReturn = $argument;
      }
      elseif ($argument instanceof Doctrine_Collection_Cachetaggable)
      {
        $tagsToReturn = $argument->getCacheTags();
      }
      elseif ($argument instanceof Doctrine_Record)
      {
        $table = $argument->getTable();

        if (! $table->hasTemplate(self::TEMPLATE_NAME))
        {
          throw new InvalidArgumentException(sprintf(
            'Object "%s" should have the "%s" template',
            $table->getClassnameToReturn(),
            self::TEMPLATE_NAME
          ));
        }

        $tagsToReturn = $argument->getCacheTags();
      }
      # Doctrine_Collection_Cachetaggable and Doctrine_Record are
      # instances of ArrayAccess
      # this check should be after them
      elseif ($argument instanceof ArrayIterator || $argument instanceof ArrayObject)
      {
        $tagsToReturn = $argument->getArrayCopy();
      }
      elseif (
          $argument instanceof IteratorAggregate
        ||
          $argument instanceof Iterator
      )
      {
        foreach ($argument as $key => $value)
        {
          $tagsToReturn[$key] = $value;
        }
      }
      else
      {
        throw new InvalidArgumentException(
          sprintf(
            'Invalid argument\'s type "%s". ' .
            'See acceptable types in the PHPDOC of "%s"',
            sprintf(
              '%s%s',
              gettype($argument),
              is_object($argument) ? '('.get_class($argument).')' : ''
            ),
            __METHOD__
          )
        );
      }

      return $tagsToReturn;
    }

    /**
     * Listens on "component.method_not_found"
     *
     * @param sfEvent $event
     * @return null
     */
    public static function listenOnComponentMethodNotFoundEvent (sfEvent $event)
    {
      try
      {
        $callable = array(
          new sfViewCacheTagManagerBridge($event->getSubject()),
          $event['method']
        );

        $value = call_user_func_array($callable, $event['arguments']);
        $event->setReturnValue($value);

        $event->setProcessed(true);
      }
      catch (BadMethodCallException $e)
      {
        $event->setProcessed(false);
      }
      catch (sfException $e)
      {
        $event->setProcessed(true);

        self::notifyApplicationLog(
          __CLASS__, $e->getMessage(), sfLogger::NOTICE
        );
      }
    }

    /**
     * If tag name provider is registerd, then it passes object class name
     * to it.
     *
     * Useful, when backend works with classes prefixed by "Backend*Models"
     * and frontend with "Frontend*Models", and tags should be equal to "Models"
     *
     * @staticvar array   $classNames   stores function calls results
     * @param     string  $className    get_class of Doctrine_Record's model
     * @return string
     */
    public static function getBaseClassName ($className)
    {
      static $classNames = array();

      $callableArray = sfConfig::get(
        'app_sfCacheTagging_object_class_tag_name_provider'
      );

      if (is_array($callableArray) && (2 == count($callableArray)))
      {
        if (! array_key_exists($className, $classNames))
        {
          $classNames[$className] = call_user_func($callableArray, $className);
        }

        return $classNames[$className];
      }

      return $className;
    }

    /**
     * @param mixed   $object   object or string, or null
     * @param string  $message
     * @param int     $priority sfLogger::* see constants
     */
    public static function notifyApplicationLog ($object, $message, $priority = null)
    {
      ProjectConfiguration::getActive()
        ->getEventDispatcher()
        ->notify(new sfEvent($object, 'application.log', array($message)))
      ;
    }

    /**
     * Collections tag name
     *
     * @return string
     */
    public static function obtainCollectionName (Doctrine_Table $table)
    {
      return self::getBaseClassName($table->getClassnameToReturn());
    }

    /**
     * Retrieves collections tags version or initialize new version if
     * nothing was before
     *
     * @return string Collection version
     */
    public static function obtainCollectionVersion ($collectionVersionName)
    {
      $collectionVersion = self::getTaggingCache()
        ->getTag($collectionVersionName);

      if (null === $collectionVersion)
      {
        $collectionVersion = self::generateVersion();
        // Set the generated version in the cache so that subsequent calls to 
        // obtainCollectionVersion return a consistent value
        self::getTaggingCache()->setTag($collectionVersionName, $collectionVersion);
      }

      return $collectionVersion;
    }


    public static function obtainTagName (Doctrine_Template_Cachetaggable $template, $objectArray)
    {
      $uniqueColumns = $template->getOptionUniqueColumns();

      $keyFormat = $template->getOptionKeyFormat($uniqueColumns);

      $table = $template->getTable();

      $columnValues = array();

      foreach ($uniqueColumns as $columnName)
      {
        if (! isset($objectArray[$columnName]) || is_object($objectArray[$columnName]))
        {
          throw new InvalidArgumentException(
            sprintf(
              'sfCacheTaggingPlugin: missing values in an array (row from table "%s") - missing key "%s"',
              get_class($table), $columnName
            )
          );
        }

        $columnValues[] = $objectArray[$columnName];
      }

      return self::buildTagKey($template, $keyFormat, $columnValues);
    }

    /**
     * Builds tag name by keyFormat and passed values
     *
     * @param string  $keyFormat
     * @param array   $values
     * @return string
     */
    public static function buildTagKey (Doctrine_Template_Cachetaggable $template, $keyFormat, array $values)
    {
      /**
       * First element is object's class name
       */
      $columnValues = array(
        sfCacheTaggingToolkit::getBaseClassName(
          $template->getTable()->getClassnameToReturn()
        )
      );

      /**
       * following elements are row's "candidate keys"
       * @link http://databases.about.com/cs/specificproducts/g/candidate.htm
       */
      $columnValues = array_merge($columnValues, $values);

      return call_user_func_array(
        'sprintf', array_merge(array($keyFormat), $columnValues)
      );
    }
  }
