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
 * @property Petition $petition
 */
class bounceComponents extends policatComponents {

  function executeList() {
    $page = isset($this->page) ? (int) $this->page : 1;

    $options = array(
        PetitionSigningTable::PETITION => $this->petition->getId(),
        PetitionSigningTable::STATUS => null,
        PetitionSigningTable::BOUNCE => true
    );

    $query = PetitionSigningTable::getInstance()->query($options);

    $this->signings = new policatPager($query, $page, 'petition_bounces_pager', array('id' => $this->petition->getId()), true, 20);
    $this->delete_token = $this->deleteCSRFToken($this->petition->getId());
  }

  private function deleteCSRFToken($id) {
    return UtilCSRF::gen('delete_signing_bounce', $id, $this->getGuardUser()->getId());
  }

}
