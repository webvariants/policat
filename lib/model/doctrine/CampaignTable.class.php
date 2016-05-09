<?php

class CampaignTable extends Doctrine_Table {

  const STATUS_ACTIVE = 1;
  const STATUS_DELETED = 2;

  static $STATUS_SHOW = array(
      self::STATUS_ACTIVE => 'active',
      self::STATUS_DELETED => 'deleted'
  );

  /**
   *
   * @return CampaignTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('Campaign');
  }

  /**
   *
   * @return Doctrine_Query
   */
  public function queryAll($deleted_too = false) {
    $query = $this->createQuery('c')->orderBy('c.id');

    if (!$deleted_too) {
      $query->where('c.status = ?', self::STATUS_ACTIVE);
    }

    return $query;
  }
  
  /**
   *
   * @return Doctrine_Query
   */
  public function queryDeleted() {
    return $this->createQuery('c')->orderBy('c.id')->where('c.status = ?', self::STATUS_DELETED);
  }

  public function findById($id, $deleted_too = false) {
    if (!is_numeric($id)) {
      return false;
    }
    
    $query = $this->queryAll($deleted_too)->andWhere('c.id = ?', $id);
    $ret = $query->fetchOne();

    $query->free();

    return $ret;
  }

  /**
   *
   * @param sfGuardUser $user
   * @return Doctrine_Query
   */
  public function queryByMember(sfGuardUser $user, $is_member = true, $deleted_too = false, $all_for_admin = true) {
    if ($all_for_admin && $user->hasPermission(myUser::CREDENTIAL_ADMIN)) {
      return $this->queryAll($deleted_too);
    }
    if ($is_member) {
      return $this->queryAll($deleted_too)->innerJoin('c.CampaignRights cr')->andWhere('c.public_enabled = ? OR (cr.user_id = ? AND cr.active = ?)', array(Campaign::PUBLIC_ENABLED_YES, $user->getId(), 1));
    }
    else {
      return $this->queryAll($deleted_too)->andWhere('c.public_enabled = ? AND c.id NOT IN (SELECT cr.campaign_id FROM CampaignRights cr WHERE cr.user_id = ? AND cr.active = ?)', array(Campaign::PUBLIC_ENABLED_NO, $user->getId(), 1));
    }
  }
  
  public function queryBillingEnabled() {
    return $this->queryAll()->andWhere('c.billing_enabled = 1');
  }

}
