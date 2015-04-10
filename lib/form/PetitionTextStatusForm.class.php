<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class PetitionTextStatusForm extends PetitionTextForm
{
  public function configure()
  {
    parent::configure();

    $this->useFields(array('id', 'status', 'updated_at'));
  }
}
