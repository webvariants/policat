<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class CampaignNewForm extends CampaignForm {
  public function configure() {
    parent::configure();

    unset($this['data_owner_id']);

  }
}
