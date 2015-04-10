<?php

  /**
   * This file is part of the sfCacheTaggingPlugin package.
   * (c) 2009-2012 Ilya Sabelnikov <fruit.dev@gmail.com>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

  /**
   * 
   *
   * @method null     setContentTags    setContentTags (mixed $tags)
   * @method null     addContentTags    addContentTags (mixed $tags)
   * @method array    getContentTags    getContentTags ()
   * @method null     removeContentTags removeContentTags ()
   * @method null     setContentTag     setContentTag (string $tagName, string $tagVersion)
   * @method boolean  hasContentTag     hasContentTag (string $tagName)
   * @method null     removeContentTag  removeContentTag (string $tagName)
   *
   * @package sfCacheTaggingPlugin
   * @subpackage util
   * @author Ilya Sabelnikov <fruit.dev@gmail.com>
   */
  class sfViewCacheTagManagerBridge
  {
    /**
     * @var sfComponent
     */
    protected $component;

    /**
     * @var sfContext
     */
    protected $context;

    /**
     * @var string
     */
    protected $namespace = null;

    /**
     * List of allowed methods to call.
     * Its made so, due to is_callable(array('sfContentTagHandler', $method))
     * throws sfCacheDisabledException
     *
     * @var array
     */
    protected $allowedCallMethods = array(
      'setContentTags',
      'addContentTags',
      'getContentTags',
      'removeContentTags',
      'setContentTag',
      'hasContentTag',
      'removeContentTag'
    );

    /**
     * @param sfComponent $component
     */
    public function __construct (sfComponent $component)
    {
      $this->component = $component;
      $this->context = $component->getContext();
    }

    /**
     * @see sfViewCacheTagManager::getTaggingCache
     * @return sfTaggingCache
     */
    protected function getTaggingCache ()
    {
      return sfCacheTaggingToolkit::getTaggingCache();
    }

    /**
     * Detects content type and returns relevent namespace name
     *
     * @return string
     */
    protected function autoDetectNamespace ()
    {
      $component = $this->component;
      $context   = $this->context;

      /**
       * Auto-detect component type:
       *  - Action with layout
       *  - Action without layout
       *  - Component
       */
      if ($component instanceof sfAction)
      {
        $viewManager = $context->getViewCacheManager();

        $uri = $viewManager->getCurrentCacheKey();

        if ($viewManager->withLayout($uri))
        {
          return sfViewCacheTagManager::NAMESPACE_PAGE;
        }
        else
        {
          return sfViewCacheTagManager::NAMESPACE_ACTION;
        }
      }
      else
      {
        return sfViewCacheTagManager::NAMESPACE_PARTIAL;
      }
    }

    /**
     * Magic method __call to proxy extra methods
     *
     * @param string  $method
     * @param array   $arguments
     * @throws
     *    BadMethodCallException    When method is invalid (even cache is off)
     *    sfCacheDisabledException  When sf_cache is turned off
     * @return null|array|boolean
     */
    public function __call ($method, $arguments)
    {
      if (! in_array($method, $this->allowedCallMethods))
      {
        throw new BadMethodCallException(sprintf(
          'Method "%s" does not exists in %s', $method, get_class($this)
        ));
      }

      if (! sfConfig::get('sf_cache'))
      {
        throw new sfCacheDisabledException('Cache "sf_cache" is disabled');
      }

      $namespace = $this->autoDetectNamespace();

      $storeInNamespace = $namespace;

      /**
       * Using partial-in-partial tags should not be overwriten
       */
      if (sfViewCacheTagManager::NAMESPACE_PARTIAL == $namespace)
      {
        $storeInNamespace = sprintf(
          '%s-_%s-%s',
          $this->component->getModuleName(),
          $this->component->getActionName(),
          $namespace
        );
      }

      $contentHandler = $this->getTaggingCache()->getContentTagHandler();

      $callable = new sfCallableArray(array($contentHandler, $method));

      array_push($arguments, $storeInNamespace);

      return $callable->callArray($arguments);
    }

    /**
     * Disables on fly action cache
     *
     * @param string $moduleName
     * @param string $actionName
     * @return boolean
     */
    public function disableCache ($moduleName = null, $actionName = null)
    {
      if (! sfConfig::get('sf_cache'))
      {
        return false;
      }

      if (! $moduleName && ! $actionName)
      {
        $moduleName = $this->component->getModuleName();
        $actionName = $this->component->getActionName();
      }

      $this
        ->context
        ->getViewCacheManager()
        ->disableCache($moduleName, $actionName)
      ;

      return true;
    }

    /**
     * Appends tags to doctrine result cache
     *
     * @param mixed                   $tags
     * @param Doctrine_Query|string   $q        Doctrine_Query or string
     * @param array                   $params   params from $q->getParams()
     *
     * @return sfViewCacheTagManagerBridge
     */
    public function addDoctrineTags ($tags, $q, array $params = array())
    {
      $key = null;

      if (is_string($q))
      {
        $key = $q;
      }
      elseif ($q instanceof Doctrine_Query)
      {
        $key = $q->getResultCacheHash($params);
      }
      else
      {
        throw new InvalidArgumentException('Invalid arguments are passed');
      }

      $tags = sfCacheTaggingToolkit::formatTags($tags);

      $this->getTaggingCache()->addTagsToCache($key, $tags);

      return $this;
    }
  }