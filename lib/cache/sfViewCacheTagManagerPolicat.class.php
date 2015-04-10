<?php

class sfViewCacheTagManagerPolicat extends sfViewCacheTagManager
{
  protected $page_tags = array();

  // call this in the action
  public function setPageTags($tags)
  {
    $this->page_tags = $tags;
  }

  public function initialize($context, sfCache $cache, $options = array())
  {
    parent::initialize($context, $cache, $options);
    $this->cache = $cache;
  }

  public function get($internalUri)
  {
    $v = parent::get($internalUri);
    if ($v instanceof stdClass) return null;
    return $v;
  }

  // much duplicated code from sfViewCacheManager, only use of $tags is new
  public function set($data, $internalUri, $tags = array())
  {
    if (!$this->isCacheable($internalUri))
    {
      return false;
    }

    try
    {
      $ret = $this->cache->set($this->generateCacheKey($internalUri), $data, $this->getLifeTime($internalUri), $tags);
    }
    catch (Exception $e)
    {
      return false;
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Save cache for "%s"', $internalUri))));
    }

    return true;
  }

  // much duplicated code from sfViewCacheManager, only use of $this->page_tags is new
  public function setPageCache($uri)
  {
    if (sfView::RENDER_CLIENT != $this->controller->getRenderMode())
    {
      return;
    }

    // save content in cache
    $saved = $this->set(serialize($this->context->getResponse()), $uri, $this->page_tags);

    if ($saved && sfConfig::get('sf_web_debug'))
    {
      $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => true)), $this->context->getResponse()->getContent())->getReturnValue();

      $this->context->getResponse()->setContent($content);
    }
  }
}