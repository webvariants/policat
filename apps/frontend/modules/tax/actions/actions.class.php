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
 * tax actions.
 *
 * @package    policat
 * @subpackage tax
 * @author     Martin
 */
class taxActions extends policatActions {

  public function executeIndex() {
    $this->notes = TaxNoteTable::getInstance()->findAll();
    $this->taxes = CountryTaxTable::getInstance()->queryAll()->execute();
  }

  public function executeNoteEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    if (isset($route_params['new'])) {
      $note = new TaxNote();
    } else {
      $note = TaxNoteTable::getInstance()->find($request->getParameter('id'));

      if (!$note) {
        return $this->notFound();
      }
    }

    $this->form = new TaxNoteForm($note);

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $this->form->save();

        return $this->ajax()->redirectRotue('tax_list')->render();
      } else {
        return $this->ajax()->form($this->form)->render();
      }
    }
  }

  public function executeNoteDelete(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $note = TaxNoteTable::getInstance()->find($id);
      /* @var $note TaxNote */
      if (!$note)
        return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('delete_country_note', $note->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#tax_note_delete_modal .modal-body')->render();

      $note->delete();
      return $this->ajax()->redirectRotue('tax_list')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'deleteNote', array('id' => $id, 'name' => $note->getName(), 'csrf_token' => $csrf_token))
        ->modal('#tax_note_delete_modal')
        ->render();
  }

  public function executeEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    if (isset($route_params['new'])) {
      $tax = new CountryTax();
    } else {
      $tax = CountryTaxTable::getInstance()->find($request->getParameter('id'));

      if (!$tax) {
        return $this->notFound();
      }
    }

    $this->form = new CountryTaxForm($tax);

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $this->form->save();

        return $this->ajax()->redirectRotue('tax_list')->render();
      } else {
        return $this->ajax()->form($this->form)->render();
      }
    }

    $this->includeChosen();
  }

  public function executeDelete(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $tax = CountryTaxTable::getInstance()->find($id);
      /* @var $tax CountryTax */
      if (!$tax)
        return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('delete_country_tax', $tax->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#tax_country_delete_modal .modal-body')->render();

      $tax->delete();
      return $this->ajax()->redirectRotue('tax_list')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $id, 'country' => $tax->getCountry(), 'csrf_token' => $csrf_token))
        ->modal('#tax_country_delete_modal')
        ->render();
  }

}
