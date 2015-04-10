<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * MailingList form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 */
class MailingListForm extends BaseMailingListForm
{
  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('target_list[%s]');

    unset(
      $this['created_at'],
      $this['updated_at'],
      $this['campaign_id'],
      $this['object_version'],
      $this['status']
    );

    $this->setWidget('name', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 2)));

    if (!$this->getObject()->isNew())
    {
      $this->setWidget('updated_at', new sfWidgetFormInputHidden());
      $this->setValidator('updated_at', new ValidatorUnchanged(array('fix' => $this->getObject()->getUpdatedAt())));
    }
  }
}
