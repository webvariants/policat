<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

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
