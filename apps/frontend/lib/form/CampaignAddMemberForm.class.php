<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class CampaignAddMemberForm extends BaseForm {

  /**
   * @var Campaign
   */
  protected $campaign;

  public function setup() {

    $campaign = $this->getOption('campaign');
    if (!$campaign || !$campaign instanceof Campaign) {
      throw new \Exception('campaign option required');
    }

    $this->campaign = $campaign;

    $this->setWidget('email', new sfWidgetFormInputText(array('label' => 'New member\'s email address'), array('placeholder' => 'example: name@domain.com')));
    $this->setValidator('email', new sfValidatorEmail(array('required' => true)));

    $this->widgetSchema->setNameFormat('addcampaignmember[%s]');
    $this->getWidgetSchema()->setFormFormatterName('bootstrap');
  }

  public function save() {
    $user = sfGuardUserTable::getInstance()->createQuery('u')
      ->where('u.email_address = ?', array($this->getValue('email')))
      ->fetchOne();

    if ($user) {
      /* @var $user sfGuardUser */

      if ($user->isCampaignMember($this->campaign)) {
        return array('success' => false, 'error' => 'User is already campaign member');
      }

      if (!$user->getIsActive()) {
        return array('success' => false, 'error' => 'User is registered but not active');
      }

      $cr = new CampaignRights();
      $cr->setCampaign($this->campaign);
      $cr->setUser($user);
      $cr->setActive(1);
      $cr->setMember(1);
      $cr->setAdmin(0);
      $cr->save();

      return array('success' => true);
    }

    $invitation = InvitationTable::getInstance()->findByEmail($this->getValue('email'));
    if (!$invitation) {
      $invitation = new Invitation();
      $invitation->setEmailAddress($this->getValue('email'));
      $invitation->setValidationCode(base_convert(sha1('invite' . $this->getValue('email') . mt_rand() . microtime() . mt_rand() . mt_rand()), 16, 36));
      $invitation->save();
    }

    $ic = InvitationCampaignTable::getInstance()->findByInvitationAndCampaign($invitation, $this->campaign);
    if ($ic) {
      return array('success' => false, 'error' => 'User invitation already sent');
    }

    $icNew = new InvitationCampaign();
    $icNew->setInvitation($invitation);
    $icNew->setCampaign($this->campaign);
    $icNew->save();

    return array('success' => true, 'message' => 'No user exists with this email address. Invitation sent by email to ' . $this->getValue('email') . '.');
  }

}
