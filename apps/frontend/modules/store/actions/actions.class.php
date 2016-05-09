<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * store actions.
 *
 * @package    policat
 * @subpackage store
 * @author     Martin
 */
class storeActions extends policatActions {

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    $this->list = StoreTable::metaOrdered();
  }

  public function executeLanguage(sfWebRequest $request) {
    $key = $request->getParameter('key');

    if (!array_key_exists($key, StoreTable::$meta))
      return $this->notFound();

    $this->key = $key;
    $this->title = StoreTable::$meta[$key]['name'];
    $this->languages = LanguageTable::getInstance()->queryAll()->execute();
    $this->list = StoreTable::metaOrdered();
  }

  public function executeEdit(sfWebRequest $request) {
    $key = $request->getParameter('key');
    if (!array_key_exists($key, StoreTable::$meta))
      return $this->notFound();

    $meta = StoreTable::$meta[$key];
    $this->title = $meta['name'];
    $this->key = $key;

    $route_params = $this->getRoute()->getParameters();
    $language_id = null;
    if (isset($route_params['type']) && $route_params['type'] == 'language') {
      $language_id = $request->getParameter('language');
      $this->language = LanguageTable::getInstance()->find($language_id);
      if (!$this->language)
        return $this->notFound();
    }

    $this->form = new StoreForm(array(), array('meta' => $meta, 'language_id' => $language_id));

    if ($request->isMethod('post')) {
      $multipart = $this->form->isMultipart();
      $this->form->bind($request->getParameter($this->form->getName()), $multipart ? $request->getFiles($this->form->getName()) : null);
      if ($this->form->isValid()) {
        $this->form->save();
        if ($language_id)
          return $this->ajax()->redirectRotue('store_language', array('key' => $key))->render($multipart);
        else
          return $this->ajax()->redirectRotue('store')->render($multipart);
      }

      return $this->ajax()->form($this->form)->render();
    }

    $this->list = StoreTable::metaOrdered();

    $this->includeIframeTransport();
    $this->includeMarkdown();
    $this->includeHighlight();
  }

}
