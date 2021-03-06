<?php

/**
 * InvitationCampaign
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class InvitationCampaign extends BaseInvitationCampaign {

  public function applyToUser(sfGuardUser $user) {
    $campaign = $this->getCampaign();

    if ($user->isCampaignMember($campaign, false)) {
      return false;
    }

    $cr = new CampaignRights();
    $cr->setCampaign($campaign);
    $cr->setUser($user);
    $cr->setActive(1);
    $cr->setMember(1);
    $cr->setAdmin(0);
    $cr->save();

    return true;
  }

}
