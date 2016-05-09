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
 * dashboard action actions.
 *
 * @package    policat
 * @subpackage api_token
 * @author     Martin
 */
class api_tokenActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition)
      return $this->notFound();

    if (!$petition->isCampaignAdmin($this->getGuardUser()))
      return $this->noAccess();

    $this->petition = $petition;
    $this->form = new EditPetitionCounterForm($petition);
    $this->tokens = PetitionApiTokenTable::getInstance()->queryByPetition($petition)->execute();
  }

  public function executeNew(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition)
      return $this->notFound();

    if (!$petition->isCampaignAdmin($this->getGuardUser()))
      return $this->noAccess();

    $token = new PetitionApiToken();
    $token->setPetition($petition);

    $code = '';
    while (strlen($code) < 30) {
      $code .= substr(base_convert(mt_rand(), 10, 36), 1, 5);
    }
    $code = substr($code, 0, 30);

    $token->setToken($code);

    $form = new PetitionApiTokenForm($token);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));

      if ($form->isValid()) {
        $form->save();

        return $this->ajax()->redirectRotue('petition_tokens', array('id' => $petition->getId()))->render();
      }

      return $this->ajax()->form($form)->render();
    }

    $this->petition = $petition;
    $this->form = $form;
  }

  public function executeEdit(sfWebRequest $request) {
    $token = PetitionApiTokenTable::getInstance()->find($request->getParameter('id'));
    /* @var $token PetitionApiToken */
    if (!$token)
      return $this->notFound();

    $petition = $token->getPetition();

    if (!$petition->isCampaignAdmin($this->getGuardUser()))
      return $this->noAccess();

    $form = new PetitionApiTokenForm($token);

    if ($request->isMethod('post')) {
      $form->bind($request->getPostParameter($form->getName()));

      if ($form->isValid()) {
        $form->save();

        return $this->ajax()->redirectRotue('petition_tokens', array('id' => $petition->getId()))->render();
      }

      return $this->ajax()->form($form)->render();
    }

    $this->petition = $petition;
    $this->form = $form;
  }

  public function executeData(sfWebRequest $request) {
    $token = PetitionApiTokenTable::getInstance()->find($request->getParameter('id'));
    /* @var $token PetitionApiToken */
    if (!$token)
      return $this->notFound();

    $petition = $token->getPetition();

    if (!$petition->isCampaignAdmin($this->getGuardUser()))
      return $this->noAccess();

    $offsets = $token->getOffsets();

    return $this->ajax()
        ->remove('#token_data_' . $token->getId())
        ->afterPartial('#token_' . $token->getId(), 'data', array('token' => $token, 'offsets' => $offsets))
        ->render();
  }

  public function executeAddnum(sfWebRequest $request) {
    $petition = PetitionTable::getInstance()->findById($request->getParameter('id'), $this->userIsAdmin());
    /* @var $petition Petition */
    if (!$petition || !$request->isMethod('post')) {
      return $this->notFound();
    }

    if (!$petition->isCampaignAdmin($this->getGuardUser())) {
      return $this->noAccess();
    }

    $form = new EditPetitionCounterForm($petition);
    $form->bind($request->getPostParameter($form->getName()), $request->getFiles($form->getName()));

    if ($form->isValid()) {
      $form->save();
      return $this->ajax()->form($form)->alert('Saved.', '', '.form-actions', 'before')->render();
    } else {
      return $this->ajax()->form($form)->render();
    }
  }

}
