<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class quotaComponents extends policatComponents {

  /**
   * @property Campaign $campaign
   */
  public function executeList() {
    $this->quotas = QuotaTable::getInstance()->queryByCamapaign($this->campaign->getId(), $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) ? array() : array(
            QuotaTable::STATUS_ORDER, QuotaTable::STATUS_BLOCKED
        ))->execute();
  }

}
