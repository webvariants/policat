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
 * Campaign form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CampaignForm extends BaseCampaignForm
{
  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('policat');

    unset(
      $this['created_at'],
      $this['updated_at'],
      $this['object_version'],
      $this['status']
    );

    $this->setWidget('name', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 4)));
    $this->setWidget('privacy_policy', new sfWidgetFormTextarea(array(), array('cols' => 90, 'rows' => 20)));
    $this->setWidget('address', new sfWidgetFormTextarea(array(), array('cols' => 80, 'rows' => 4)));

    $this->setWidget('owner_register', new sfWidgetFormChoice(array('choices' => Campaign::$OWNER_REGISTER_SHOW, 'expanded' => true)));
    $this->setValidator('owner_register', new sfValidatorChoice(array('choices' => array_keys(Campaign::$OWNER_REGISTER_SHOW))));
    $this->setWidget('allow_download', new sfWidgetFormChoice(array('choices' => Campaign::$ALLOW_DOWNLOAD_SHOW, 'expanded' => true)));
    $this->setValidator('allow_download', new sfValidatorChoice(array('choices' => array_keys(Campaign::$ALLOW_DOWNLOAD_SHOW))));

    $this->widgetSchema->setLabel('sf_guard_user_list', 'Members');
    $this->widgetSchema->setLabel('privacy_policy', 'Privacy agreement');
  }
}
