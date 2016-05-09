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
 * mapping actions.
 *
 * @package    policat
 * @subpackage mapping
 * @author     Martin
 */
class mappingActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    $this->mappings = MappingTable::getInstance()->queryAll()->execute();
  }

  public function executeEdit(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    if (isset($route_params['new'])) {
      $mapping = new Mapping();
    } else {
      $mapping = MappingTable::getInstance()->find($request->getParameter('id'));

      if (!$mapping)
        return $this->notFound();
    }

    $this->form = new MappingForm($mapping);

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $this->form->save();

        return $this->ajax()->redirectRotue('mapping_index')->render();
      }
      else
        return $this->ajax()->form($this->form)->render();
    }

    if (!$mapping->isNew()) {
      $this->mapping = $mapping;
      $query = MappingPairTable::getInstance()->queryByMappingId($mapping->getId());
      $this->pairs = new policatPager($query, 1, 'mapping_pair_pager', array('id' => $mapping->getId()), true, 20);
    }
  }

  public function executePairPager(sfWebRequest $request) {
    $mapping = MappingTable::getInstance()->find($request->getParameter('id'));

    if (!$mapping)
      return $this->notFound();

    $query = MappingPairTable::getInstance()->queryByMappingId($mapping->getId());
    $pairs = new policatPager($query, $request->getParameter('page'), 'mapping_pair_pager', array('id' => $mapping->getId()), true, 20);

    return $this->ajax()->replaceWithPartial('#pairs_pager', 'pairs', array('pairs' => $pairs, 'mapping' => $mapping))->render();
  }

  public function executeEditPair(sfWebRequest $request) {
    $route_params = $this->getRoute()->getParameters();
    if (isset($route_params['new'])) {
      $mapping = MappingTable::getInstance()->find($request->getParameter('id'));
      if (!$mapping)
        return $this->notFound();

      $pair = new MappingPair();
      $pair->setMapping($mapping);
    } else {
      $pair = MappingPairTable::getInstance()->find($request->getParameter('id'));

      if (!$pair)
        return $this->notFound();

      /* @var $pair MappingPair */

      $mapping = $pair->getMapping();
    }

    $form = new MappingPairForm($pair);
    $was_new = $pair->isNew();

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));

      if ($form->isValid()) {
        $form->save();

        if ($was_new) {
          return $this->ajax()->remove('#pair_form_new')->appendPartial('#pairs tbody', 'pair_row', array('pair' => $pair))->render();
        } else {
          return $this->ajax()->remove('#pair_form_' . $pair->getId())->replaceWithPartial('#pair_' . $pair->getId(), 'pair_row', array('pair' => $pair))->render();
        }
      }
      else
        return $this->ajax()->form($form)->render();
    }

    if ($was_new) {
      return $this->ajax()->remove('#pair_form_new')->appendPartial('#pairs tbody', 'pair_form', array('pair' => $pair, 'form' => $form))->render();
    } else {
      return $this->ajax()->remove('#pair_form_' . $pair->getId())->afterPartial('#pair_' . $pair->getId(), 'pair_form', array('pair' => $pair, 'form' => $form))->render();
    }
  }

  public function executeDeletePair(sfWebRequest $request) {
    $pair = MappingPairTable::getInstance()->find($request->getParameter('id'));

    if (!$pair)
      return $this->notFound();

    /* @var $pair MappingPair */

    $form = new BaseForm();
    $form->getWidgetSchema()->setNameFormat('delete_pair[%s]');
    $form->bind($request->getPostParameter($form->getName()));
    if ($form->isValid()) {
      $id = $pair->getId();
      $pair->delete();
      return $this->ajax()->remove('#pair_' . $id)->remove('#pair_form_' . $id)->render();
    }
    else
      return $this->ajax()->form($form)->render();
  }

}