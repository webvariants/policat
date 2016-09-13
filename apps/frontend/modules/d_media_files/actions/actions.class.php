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
 * dashboard widget actions.
 *
 * @package    policat
 * @subpackage d_media_files
 * @author     Martin
 */
class d_media_filesActions extends policatActions {

  /**
   *
   * @param sfWebRequest $request
   * @return Petition
   */
  private function getPetition(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition) {
      $this->notFound('Not found.', true);
    }
    if (!$petition->isEditableBy($this->getGuardUser())) {
      $this->noAccess('Access denied.', 'Access denied.', true);
    }

    return $petition;
  }

  public function executeIndex(sfWebRequest $request) {
    $this->petition = $this->getPetition($request);
    $media_file = new MediaFile();
    $media_file->setPetition($this->petition);
    $this->form = new MediaFileForm($media_file);

    $this->includeIframeTransport();
  }

  public function executePager(sfWebRequest $request) {
    $page = $request->getParameter('page', 1);
    $petition = $this->getPetition($request);
    return $this->ajax()->replaceWithComponent('#media_files_list', 'd_media_files', 'list', array('page' => $page, 'petition' => $petition))->render();
  }

  public function executeDelete(sfWebRequest $request) {
    $media_file = MediaFileTable::getInstance()->findOneById($request->getParameter('id'));
    if (!$media_file) {
      return $this->ajax()->alert('Not found')->render();
    }

    /* @var $media_file MediaFile */
    $petition = $media_file->getPetition();

    if (!$petition->isEditableBy($this->getGuardUser())) {
      $this->noAccess('Access denied.', 'Access denied.', true);
    }

    $csrf_token = UtilCSRF::gen('delete_media_file', $media_file->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token) {
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#media_file_delete_modal .modal-body')->render();
      }

      $this->ajax()->redirectRotue('media_files_list', array('id' => $petition->getId()));
      $media_file->delete();
      return $this->ajax()->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $media_file->getId(), 'name' => $media_file->getTitle(), 'csrf_token' => $csrf_token))
        ->modal('#media_file_delete_modal')
        ->render();
  }

  public function executeRename(sfWebRequest $request) {
    $media_file = MediaFileTable::getInstance()->findOneById($request->getParameter('id'));
    if (!$media_file) {
      return $this->ajax()->alert('Not found')->render();
    }

    /* @var $media_file MediaFile */
    $petition = $media_file->getPetition();

    if (!$petition->isEditableBy($this->getGuardUser())) {
      $this->noAccess('Access denied.', 'Access denied.', true);
    }

    $form = new MediaFileForm($media_file);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));

      if (!$form->isValid()) {
        return $this->ajax()->form($form)->render();
      }

      $form->save();
      return $this->ajax()->redirectRotue('media_files_list', array('id' => $petition->getId()))->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'rename', array('form' => $form))
        ->modal('#media_file_rename_modal')
        ->focus('#media_file_title')
        ->render();
  }

  public function executeUpload(sfWebRequest $request) {
    $petition = $this->getPetition($request);
    $media_file = new MediaFile();
    $media_file->setPetition($petition);
    $form = new MediaFileForm($media_file);
    $form->bind($request->getPostParameter($form->getName()), $request->getFiles($form->getName()));

    if (!$form->isValid()) {
      return $this->ajax()->form($form)->render(true);
    }

    $form->save();

    return $this->ajax()->redirectRotue('media_files_list', array('id' => $petition->getId()))->render(true);
  }

}
