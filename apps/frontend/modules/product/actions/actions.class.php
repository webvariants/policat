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
 * product actions.
 *
 * @package    policat
 * @subpackage product
 * @author     Martin
 */
class productActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    $this->list = ProductTable::getInstance()->queryAll()->execute();
  }

  public function executeEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    if (isset($route_params['new'])) {
      $product = new Product();
    } else {
      $product = ProductTable::getInstance()->find($request->getParameter('id'));

      if (!$product) {
        return $this->notFound();
      }
    }

    $this->form = new ProductForm($product);

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $this->form->save();

        return $this->ajax()->redirectRotue('product_index')->render();
      } else {
        return $this->ajax()->form($this->form)->render();
      }
    }

    $this->includeChosen();
  }

  public function executeDelete(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $product = ProductTable::getInstance()->find($id);
      /* @var $product Product */
      if (!$product)
        return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('delete_product', $product->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#product_delete_modal .modal-body')->render();

      $product->delete();
      return $this->ajax()->redirectRotue('product_index')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $id, 'name' => $product->getName(), 'csrf_token' => $csrf_token))
        ->modal('#product_delete_modal')
        ->render();
  }

}
