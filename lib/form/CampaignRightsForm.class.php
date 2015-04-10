<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class CampaignRightsForm extends CampaignForm
{
  function configure()
  {
    parent::configure();

    $this->useFields(array('id'));
    $this->embedRelation('Member', 'MemberRightsForm');
    $this->addSaveManyToMany(array('Member'), array('group_list' => 'Group'));

    foreach ($this['Member'] as $key => $value) $this->widgetSchema['Member'][$key]->setLabel(false);
    $this->widgetSchema['Member']->setLabel('Member rights');
  }
}
