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
 * PetitionContact form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PetitionContactForm extends BasePetitionContactForm {

  public function configure() {
    $petition_contact = $this->getObject();
    $petition = $petition_contact->getPetition();
    $contact = $petition_contact->getContact();
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('petition_contact[%s]');
    $this->widgetSchema->setNameFormat('petition_contact_' . $petition->getId() . '_' . $contact->getId() . '[%s]');

    if ($this->getObject()->getPetition()->getPledgeWithComments()) {
      $this->useFields(array('comment'));
      $this->getWidget('comment')->setAttribute('class', 'span8');
    } else {
      $this->useFields();
    }

    $pledge_items = PledgeItemTable::getInstance()->fetchByIds($petition->getActivePledgeItemIds());
    $pledge_table = PledgeTable::getInstance();

    foreach ($pledge_items as $pledge_item) {
      /* @var $pledge_item PledgeItem */

      $pledge = $pledge_table->findOneByPledgeItemAndContact($pledge_item, $contact);

      $this->setWidget('pledge_' . $pledge_item->getId(), new sfWidgetFormChoice(array(
          'choices' => PledgeTable::$STATUS_CHOICES,
          'default' => $pledge ? $pledge->getStatus() : null,
          'label' => $pledge_item->getName()
      )));

      $this->setValidator('pledge_' . $pledge_item->getId(), new sfValidatorChoice(array('choices' => array_keys(PledgeTable::$STATUS_CHOICES))));
    }
  }

  protected function doSave($con = null) {
    if (null === $con) {
      $con = $this->getConnection();
    }
    parent::doSave($con);

    $petition_contact = $this->getObject();
    $petition = $petition_contact->getPetition();
    $contact = $petition_contact->getContact();
    $pledge_items = PledgeItemTable::getInstance()->fetchByIds($petition->getActivePledgeItemIds());
    $pledge_table = PledgeTable::getInstance();


    foreach ($pledge_items as $pledge_item) {
      /* @var $pledge_item PledgeItem */

      $pledge = $pledge_table->findOneByPledgeItemAndContact($pledge_item, $contact);

      if (!$pledge) {
        $pledge = new Pledge();
        $pledge->setPledgeItem($pledge_item);
        $pledge->setContact($contact);
      }

      $status = $this->getValue('pledge_' . $pledge_item->getId());

      if ($status != $pledge->getStatus()) {
        $pledge->setStatus($status);
        $pledge->setStatusAt(gmdate('Y-m-d H:i:s'));
        $pledge->save($con);
      }
    }
  }

}
