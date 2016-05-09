<?php

class sfGuardUserPermissionTable extends PluginsfGuardUserPermissionTable {

  /**
   *
   * @return sfGuardUserPermissionTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('sfGuardUserPermission');
  }

  public function deleteUserPermission(sfGuardUser $user) {
    $this->createQuery('up')
      ->leftJoin('up.Permission p')
      ->where('up.user_id = ?', $user->getId())
      ->andWhere('p.name = ?', myUser::CREDENTIAL_USER)
      ->execute()
      ->delete();
  }

}