<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class signersActions extends policatActions {

  public function executeIndex(sfWebRequest $request) {
    $response = $this->getResponse();

    // determine the requested action (petition)
    $action_id = $request->getParameter('id');
    if (!is_numeric($action_id) || $action_id < 0) {
      $this->forward404();
    }

    $text_id = $request->getParameter('text_id');
    if (!is_numeric($text_id) || $text_id < 0) {
      $this->forward404();
    }

    $petition = PetitionTable::getInstance()->findByIdCachedActive($action_id);
    if (!$petition) {
      $this->forward404();
    }

    $petition_text = PetitionTextTable::getInstance()->findByIdCachedActive($text_id);
    if (!$petition_text) {
      $this->forward404();
    }

    $this->data = array(
        'id' => $petition->getId(),
        'text_id' => $petition_text->getId(),
    );
    $this->setLayout('clean');
  }

}
