<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatPager extends sfDoctrinePager {

  protected $route;
  protected $params;
  protected $ajax;

  /** @var policatFilterForm */
  protected $filter = null;

  /**
   *
   * @param string $class
   * @param Doctrine_Query $query
   * @param int $page
   * @param string $route
   * @param array $params
   * @param bool $ajax
   * @param int $maxPerPage
   */
  public function __construct(Doctrine_Query $query, $page = 1, $route = null, $params = array(), $ajax = true, $maxPerPage = 10, $filter = null) {
    parent::__construct(null, $maxPerPage);
    if ($filter) {
      if ($filter instanceof policatFilterForm) {
        if ($filter->isValid()) {
          $query = $filter->filter($query);
          $this->filter = $filter;
        }
      }
      else
        throw new Exception('filter must be of type policatFilterForm');
    }
    $this->setQuery($query);
    $this->setPage($page);
    $this->init();
    $this->route = $route;
    $this->params = $params;
    $this->ajax = $ajax;
  }

  public function getRoute() {
    return $this->route;
  }

  public function getParams() {
    return $this->params;
  }

  public function getAjax() {
    return $this->ajax;
  }

  public function getUrl($page = 1, $absolute = false) {
    $params = $this->getParams();
    $params['page'] = $page;
    $query_param = '';

    if ($this->filter && $this->filter->isValid())
      $query_param = http_build_query($this->filter->getQueryParams());

    return sfContext::getInstance()->getRouting()->generate($this->getRoute(), $params, $absolute) . ($query_param ? '?' . $query_param : '');
  }

  public function getPageNumbers($close = 2, $end = 2, $page = null) {
    if ($page === null)
      $page = $this->getPage();
    $page = (int) $page;

    $ret = array();
    for ($i = 0; $i < $end; $i++) {
      $ret[] = 1 + $i;
      $ret[] = $this->getLastPage() - $i;
    }
    for ($i = 0; $i <= $close; $i++) {
      $ret[] = $page - $i;
      $ret[] = $page + $i;
    }

    $ret = array_unique($ret);
    sort($ret);
    return array_filter($ret, array($this, 'okPage'));
  }

  public function getPrev($page = null) {
    if ($page === null)
      $page = $this->getPage();
    $page = (int) $page;

    $page = $page - 1;
    if ($this->okPage($page))
      return $page;
    else
      return null;
  }

  public function getNext($page = null) {
    if ($page === null)
      $page = $this->getPage();
    $page = (int) $page;

    $page = $page + 1;
    if ($this->okPage($page))
      return $page;
    else
      return null;
  }

  public function okPage($number) {
    return $number >= 1 && $number <= $this->getLastPage();
  }

}

