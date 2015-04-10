<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * api actions.
 *
 * @package    policat
 * @subpackage api
 * @author     Martin
 */
class apiActions extends policatActions {

  public function executeDoc(sfWebRequest $request) {
  }

  public function getWidgetStatus($widget_id) {
    if (!is_numeric($widget_id))
      return null;
    $cache = $this->getContext()->getViewCacheManager()->getCache();
    if ($cache instanceof sfTagCache) $cache = $cache->getCache();
    $key = 'api_widget_status_' . ((int) $widget_id);
    $data = $cache->get($key, null);
    if ($data !== null) {
      $data['timeout'] = $cache->getTimeout($key);
      return $data;
    }


    $data = WidgetTable::getInstance()->fetchStatus($widget_id);
    if ($data && is_array($data)) $data = reset($data);
    if (!is_array($data)) $data = array();

    $cache->set($key, $data, 600);
    $data['timeout'] = $cache->getTimeout($key);
    return $data;
  }

  /**
   * Executes counter action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    $this->setLayout(false);
    $json = array('status' => 'error');
    $this->getResponse()->setContentType('application/json');
    //$this->getResponse()->setContentType('text/plain');

    $widget_id = $request->getParameter('widget_id');
    $json = $this->getWidgetStatus($widget_id);
    if (is_array($json)) {
			unset($json['stylings']);
      if (array_key_exists('widget_id', $json)) $json['status'] = 'ok';
      else $json['status'] = 'error';

      if (array_key_exists('timeout', $json))
        $this->getResponse()->addCacheControlHttpHeader('max-age', $json['timeout'] - time());
    }
    else
      $json = array('status' => 'error');

    $this->getResponse()->addCacheControlHttpHeader('public');

    switch ($request->getParameter('format')) {
      case 'json':
        return $this->renderText(json_encode($json));
      case 'jsonp':
        $callback = $request->getParameter('callback', 'callback');
        if (!is_scalar($callback))
          $callback = 'callback';

        // prepend callback with an empty comment to prevent CVE-2014-4671
        // http://miki.it/blog/2014/7/8/abusing-jsonp-with-rosetta-flash/
        return $this->renderText('/**/' . $callback . '(' . json_encode($json) . ');');
    }
    return $this->renderText('');
  }

	/**
   * Executes ajax action get stylings for widget
   *
   * @param sfRequest $request A request object
   */
  public function executeColors(sfWebRequest $request) {
    $this->setLayout(false);
    $json = array('status' => false);
    $this->getResponse()->setContentType('application/json');
    //$this->getResponse()->setContentType('text/plain');

    $widget_id = $request->getParameter('widget_id');
    $json = $this->getWidgetStatus($widget_id);
    if (is_array($json)) {
      if (array_key_exists('widget_id', $json)) $json['status'] = true;
      else $json['status'] = false;

      if (array_key_exists('timeout', $json))
        $this->getResponse()->addCacheControlHttpHeader('max-age', $json['timeout'] - time());
    }
    else
      $json = array('status' => false);

    $this->getResponse()->addCacheControlHttpHeader('public');

    return $this->renderText(json_encode($json));
  }

	public function executeCounterbar_generator(sfWebRequest $request) {
    $response = $this->getResponse();
    if ($response instanceof sfWebResponse) {
      $response->addStylesheet('counterbar.css', 'last');
      $response->addJavascript('counterbar_generator.js', 'last');
      $response->addJavascript('jscolor.min.js', 'last');
    }
	}

  public function executeCounterbar(sfWebRequest $request) {
		$widget = Doctrine_Core::getTable('Widget')->find($request->getParameter('id'));
    /* @var $widget Widget */

    $this->count = $widget->getPetition()->getCount(60);
    $this->target = Petition::calcTarget($this->count, $widget->getPetition()->getTargetNum());
    $this->lang = $widget->getPetitionText()->getLanguageId();
    $this->getUser()->setCulture($this->lang);
    $this->stylings = json_decode($widget->getStylings(), true);
		$this->widgetid = $widget->getId();
		$this->markup = $this->getPartial('counterbar', array('count' => $this->count, 'target' => $this->target, 'stylings' => $this->stylings, 'widgetid' => $this->widgetid));

    $this->getResponse()->setContentType('text/javascript');
    $this->setLayout(false);
  }
}
