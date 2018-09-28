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

    $inviteBy = $this->getOption('invite_by');
    if (!$inviteBy || !$inviteBy instanceof sfGuardUser) {
      throw new \Exception('invite_by (sfGuardUser) option required');
    }

    $this->campaign = $campaign;

    $this->setWidget('email', new sfWidgetFormInputText(array('label' => 'New member\'s email address'), array('placeholder' => 'example: name@domain.com')));
    $this->setValidator('email', new sfValidatorEmail(array('required' => true)));

    $this->widgetSchema->setNameFormat('addcampaignmember[%s]');
    $this->getWidgetSchema()->setFormFormatterName('bootstrap4');
  }

  public function save() {
    $user = sfGuardUserTable::getInstance()->createQuery('u')
      ->where('u.email_address = ?', array($this->getValue('email')))
      ->fetchOne();

    if ($user) {
      /* @var $user sfGuardUser */

      if ($user->isCampaignMember($this->campaign, false)) {
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

    if (!StoreTable::value(StoreTable::REGISTER_ON)) {
      return array('success' => false, 'error' => 'Can not invite user, registration is disabled.');
    }

    $invitation = InvitationTable::getInstance()->findByEmail($this->getValue('email'));
    if (!$invitation) {
      $invitation = new Invitation();
      $invitation->setEmailAddress($this->getValue('email'));
      $invitation->setValidationCode(substr(base_convert(sha1('invite' . $this->getValue('email') . mt_rand() . microtime() . mt_rand() . mt_rand()), 16, 36), 0, 40));
    }
    $invitation->setExpiresAt(gmdate('Y-m-d H:i:s', time() + 60 * 60 * 24)); // expire after 1 day
    $invitation->save();

    $ic = InvitationCampaignTable::getInstance()->findByInvitationAndCampaign($invitation, $this->campaign);
    if ($ic) {
      return array('success' => false, 'error' => 'User invitation already sent');
    }

    $icNew = new InvitationCampaign();
    $icNew->setInvitation($invitation);
    $icNew->setCampaign($this->campaign);
    $icNew->setInvitedBy($this->getOption('invite_by'));
    $icNew->save();

    $this->sendEmail($icNew);

    return array('success' => true, 'message' => 'No user exists with this email address. Invitation sent by email to ' . $this->getValue('email') . '.');
  }

  private function sendEmail(InvitationCampaign $invitationCampaign) {
    $invitation = $invitationCampaign->getInvitation();
    $sender = $invitationCampaign->getInvitedBy();

    $senderName = strtr($sender->getFullName(), array('[' => '', ']' => '', '(' => '', ')' => '', '<' => '', '>' => ''));
    $senderEmail = $sender->getEmailAddress();
    $campaignName = strtr($invitationCampaign->getCampaign()->getName(), array('[' => '', ']' => '', '(' => '', ')' => '', '<' => '', '>' => ''));
    $email = $invitation->getEmailAddress();
    $code = $invitation->getId() . '-' . $invitation->getValidationCode();
    $registerUrl = sfContext::getInstance()->getRouting()->generate('register', array(), true) . '?invitation=' . $code;
    $invitationUrl = sfContext::getInstance()->getRouting()->generate('invitation', array(), true) . '?code=' . $code;

    $homepage = sfContext::getInstance()->getRouting()->generate('homepage', array(), true);

    $portalName = StoreTable::value(StoreTable::PORTAL_NAME);
    $subject = $portalName . ' invitation';
    $body = <<<EOT
<div markdown="1" class="frame">
#$subject

[$senderName](mailto:$senderEmail) invited you to join campaign $campaignName as a member/editor.
There is no user account with your [$email](mailto:$email) yet.
Register a new user account, or transfer the invitation to an existing user account with a different email address.

[Register]($registerUrl)
[Login and transfer invitation to existing account]($invitationUrl)

If you received this message by mistake, you may simply ignore it.
</div>
<div markdown="1" class="footer">
[$portalName]($homepage)
</div>
EOT;

    UtilMail::send('CampaignInvite', 'Invitation-' . $invitation->getId() , null, $invitation->getEmailAddress(), $subject, $body, null, null, null, null, array(), true);
  }
}
