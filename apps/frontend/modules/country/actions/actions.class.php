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
 * country actions.
 *
 * @package    policat
 * @subpackage country
 * @author     Martin
 */
class countryActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    $this->list = CountryCollectionTable::getInstance()->queryAll()->execute();
  }

  public function executeEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    if (isset($route_params['new'])) {
      $collection = new CountryCollection();
    } else {
      $collection = CountryCollectionTable::getInstance()->find($request->getParameter('id'));

      if (!$collection)
        return $this->notFound();
    }

    $this->form = new CountryCollectionForm($collection);

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $this->form->save();

        return $this->ajax()->redirectRotue('country_index')->render();
      } else
        return $this->ajax()->form($this->form)->render();
    }

    $this->includeChosen();
  }

}
