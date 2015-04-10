<?php

class sfGuardUserTable extends PluginsfGuardUserTable {

  /**
   *
   * @return sfGuardUserTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('sfGuardUser');
  }

  const VALIDATION_KIND_NONE = 0;
  const VALIDATION_KIND_REGISTER_LINK = 1;
  const VALIDATION_KIND_BACKEND_LINK = 2;

  /**
   *
   * @param int $id
   * @param string $code
   * @param bool $active 
   * 
   * @return sfGuardUser
   */
  public function getByRegisterValidationByLink($id, $code, $active = false) {
    $query = self::getInstance()->createQuery('u');
    if (is_bool($active))
      $query->where('u.is_active = ?', $active);

    return $query->andWhere('u.id = ?', $id)
        ->andWhere('u.validation_code = ?', $code)
        ->andWhere('u.validation_kind = ?', self::VALIDATION_KIND_REGISTER_LINK)
        ->fetchOne();
  }

  public function getByPasswordForgottenCode($id, $code) {
    if (!is_string($code) || strlen($code) < 8) {
      return null;
    }
    $query = self::getInstance()->createQuery('u');
    $user = $query
      ->andWhere('u.id = ?', $id)
      ->fetchOne();

    if ($user) {
      /* @var $user sfGuardUser */

      $forgot_passwort = $user->getForgotPassword();
      if (!$forgot_passwort) {
        return null;
      }
      $key = $forgot_passwort->getUniqueKey();
      $expire = $forgot_passwort->getExpiresAt();
      if ($expire > (time() - 24 * 3600)) {
        return null;
      }

      if (!$key) {
        return null;
      }

      if (crypt($code, $key) === $key) {
        return $user;
      }
    }

    return null;
  }

  /**
   *
   * @param int $id
   * @param string $code
   * @param bool $active 
   * 
   * @return sfGuardUser
   */
  public function getByValidationBackendByLink($id, $code, $active = false) {
    $query = self::getInstance()->createQuery('u');
    if (is_bool($active))
      $query->where('u.is_active = ?', $active);

    return $query->andWhere('u.id = ?', $id)
        ->andWhere('u.validation_code = ?', $code)
        ->andWhere('u.validation_kind = ?', self::VALIDATION_KIND_BACKEND_LINK)
        ->fetchOne();
  }

  /**
   *
   * @return Doctrine_Query
   */
  public function queryAll($super_admin_too = false) {
    $query = $this->createQuery('u')->orderBy('u.id ASC');
    if (!$super_admin_too)
      $query->andWhere('u.is_super_admin != true');

    return $query;
  }

  /**
   *
   * @param Campaign $campaign
   * @return Doctrine_Query
   */
  public function queryByCampaign(Campaign $campaign) {
    return $this->createQuery('u')->orderBy('u.id')
        ->leftJoin('u.CampaignRights cr')
        ->where('cr.campaign_id = ?', $campaign->getId())
    ;
  }

  /**
   *
   * @param Campaign $campaign
   * @return Doctrine_Query
   */
  public function queryAdminsByCampaign(Campaign $campaign, sfGuardUser $orUser = null) {
    $query = $this->queryByCampaign($campaign);
    if ($orUser)
      $query->andWhere('(cr.admin = 1 AND cr.active = 1) OR u.id = ?', $orUser->getId());
    else
      $query->andWhere('cr.admin = 1 AND cr.active = 1');

    return $query;
  }

}
