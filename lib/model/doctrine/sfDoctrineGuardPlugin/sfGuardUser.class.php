<?php

/**
 * sfGuardUser
 *
 * @package    policat
 * @subpackage model
 * @author     Martin
 */
class sfGuardUser extends PluginsfGuardUser {

  public function getFullName() {
    $f = $this->getFirstName();
    $l = $this->getLastName();
    if ($f && $l)
      return $f . ' ' . $l;
    if ($f)
      return $f;
    return $l;
  }

  private $camapign_admin_ids = null;

  public function getCampaignAdminIds() {
    if ($this->camapign_admin_ids !== null)
      return $this->camapign_admin_ids;
    return $this->camapign_admin_ids = CampaignRightsTable::getInstance()->adminIds($this);
  }

  /**
   *
   * @param Campaign $campaign or ID
   * @return bool
   */
  public function isCampaignAdmin($campaign) {
    if ($this->hasPermission(myUser::CREDENTIAL_ADMIN))
      return true;
    if (is_numeric($campaign))
      $id = $campaign;
    elseif ($campaign instanceof Campaign)
      $id = $campaign->getId();
    else
      return null;
    return in_array($id, $this->getCampaignAdminIds());
  }

  private $cr_cache = array();

  /**
   *
   * @param Campaign $campaign
   * @return CampaignRights
   */
  public function getRightsByCampaign(Campaign $campaign) {
    if (array_key_exists($campaign->getId(), $this->cr_cache))
      return $this->cr_cache[$campaign->getId()];
    return $this->cr_cache[$campaign->getId()] = CampaignRightsTable::getInstance()->queryByCampaignAndUser($campaign, $this)->fetchOne();
  }

  public function isCampaignMember(Campaign $campaign, $permissions = true) {
    if ($permissions && ($this->hasPermission(myUser::CREDENTIAL_ADMIN) || $campaign->getPublicEnabled() == Campaign::PUBLIC_ENABLED_YES)) {
      return true;
    }
    $cr = $this->getRightsByCampaign($campaign);
    return $cr && $cr->getActive() && ($cr->getMember() || $cr->getAdmin());
  }

  private $pr_cache = array();

  /**
   *
   * @param Petition $petition
   * @return PetitionRights
   */
  public function getRightsByPetition(Petition $petition) {
    if (array_key_exists($petition->getId(), $this->pr_cache))
      return $this->pr_cache[$petition->getId()];
    return $this->pr_cache[$petition->getId()] = PetitionRightsTable::getInstance()->queryByPetitionAndUser($petition, $this)->fetchOne();
  }

  public function isPetitionMember(Petition $petition, $orCampaignAdmin = false) {
    if ($orCampaignAdmin && $this->isCampaignAdmin($petition->getCampaign()))
      return true;
    $pr = $this->getRightsByPetition($petition);
    return $pr && $pr->getActive() && $pr->getMember() && $this->isCampaignMember($petition->getCampaign());
  }

  public function randomValidationCode() {
    $this->setValidationCode(substr(base_convert(sha1($this->getEmailAddress() . uniqid(mt_rand(), true)), 16, 36), 0, 20));
  }

  private $tr_cache = array();

  /**
   *
   * @param MailingList $mailing_list
   * @return TargetListRights
   */
  public function getTargetListRights(MailingList $mailing_list) {
    if (array_key_exists($mailing_list->getId(), $this->tr_cache))
      return $this->tr_cache[$mailing_list->getId()];
    return $this->tr_cache[$mailing_list->getId()] = TargetListRightsTable::getInstance()->queryByTargetListAndUser($mailing_list, $this)->fetchOne();
  }

  public function isTargetListMember(MailingList $mailing_list, $orCampaignAdmin = false, $andCampaignMember = true) {
    if ($orCampaignAdmin && $this->isCampaignAdmin($mailing_list->getCampaign()))
      return true;
    if ($andCampaignMember && !$this->isCampaignMember($mailing_list->getCampaign()))
      return false;
    $tr = $this->getTargetListRights($mailing_list);
    return $tr && $tr->getActive();
  }

  public function isDataOwnerOfCampaign(Campaign $campaign) {
    return $this->getId() == $campaign->getDataOwnerId();
  }

  public function hasValidEmail() {
    $email = $this->getEmailAddress();
    return is_string($email) && preg_match(ValidatorEmail::REGEX_EMAIL, $email);
  }

  public function getSwiftEmail() {
    if ($this->hasValidEmail()) {
      $name = $this->getFullName();
      if (is_string($name) && $name)
        return array($this->getEmailAddress() => $name);
      return $this->getEmailAddress();
    }
    return null;
  }

  public function getFromNameWithOrganisation() {
    return $this->getFullName() . ($this->getOrganisation() ? ' (' . $this->getOrganisation()  . ')' : '');
  }

  public function hasCampaigns() {
    return CampaignTable::getInstance()->queryByMember($this)->limit(1)->count();
  }

  public function setPassword($password) {
    if (!$password && 0 == strlen($password)) {
      return;
    }

    $salt = '';
    while (strlen($salt) < 16) {
      $salt .= base_convert(mt_rand(), 10, 36);
    }
    $salt = '$6$' . substr($salt, 0, 16);

    $this->setAlgorithm('crypt');
    $this->_set('password', crypt($password, $salt));
  }

  public function checkPassword($password) {
    $algorithm = $this->getAlgorithm();
    if ($algorithm === 'crypt') {
      return hash_equals(crypt($password, $this->getPassword()), $this->getPassword());
    }

    return parent::checkPassword($password);
  }

}
