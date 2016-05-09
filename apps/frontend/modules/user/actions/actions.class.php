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
 * user actions.
 *
 * @package    policat
 * @subpackage user
 * @author     Martin
 */
class userActions extends policatActions {

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {

  }

  public function executePager(sfWebRequest $request) {
    $page = $request->getParameter('page', 1);
    return $this->ajax()->replaceWithComponent('#user_list', 'user', 'list', array('page' => $page, 'no_filter' => true))->render();
  }

  public function executeEdit(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $user = sfGuardUserTable::getInstance()->find($id);
      /* @var $user sfGuardUser */
      if (!$user)
        return $this->notFound();
    }
    else {
      $user = new sfGuardUser();
      $user->setIsActive(false);
    }
    if (!$this->getGuardUser()->getIsSuperAdmin() && $user->getIsSuperAdmin())
      $this->noAccess();

    if ($user->isNew())
      $this->form = new UserNewForm($user);
    else
      $this->form = new UserForm($user);
    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $con = sfGuardUserTable::getInstance()->getConnection();
        $con->beginTransaction();
        try {
          $this->form->updateGroupsList($this->form->getValues());
          $user = $this->form->updateObject();
          $user->setUsername($user->getEmailAddress());

          if ($user->isNew()) {
            $user->setValidationKind(sfGuardUserTable::VALIDATION_KIND_BACKEND_LINK);
            $user->randomValidationCode();
            $user->save();

            $subject = 'validate activation';
            $body = "#VALIDATION-URL#";

            $store = StoreTable::getInstance()->findByKeyAndLanguageWithFallback(StoreTable::NEW_USER_ADMIN_MAIL, $user->getLanguageId());
            if ($store) {
              $subject = $store->getField('subject');
              $body = $store->getField('body');
            }

            $subst = array(
                '#VALIDATION-URL#' => $this->generateUrl('user_validation', array('id' => $user->getId(), 'code' => $user->getValidationCode()), true),
                '#USER-NAME#' => $user->getFullName()
            );

            UtilMail::send(null, null, $user->getEmailAddress(), $subject, $body, null, $subst);
          } else
            $user->save();

          $con->commit();
        } catch (Exception $e) {
          $con->rollback();
          throw $e;
        }

        return $this->ajax()->redirectRotue('user_idx')->render();
      } else {
        return $this->ajax()->form($this->form)->render();
      }
    }
  }

  public function executeValidation(sfWebRequest $request) {
    $id = $request->getParameter('id');
    $code = $request->getParameter('code');

    $user = sfGuardUserTable::getInstance()->getByValidationBackendByLink($id, $code, false);

    if ($user) {
      $this->user = $user;

      $this->form = new UserPasswordForm($user);

      if ($request->isMethod('post')) {
        $this->form->bind($request->getPostParameter($this->form->getName()));

        if ($this->form->isValid()) {
          $user = $this->form->updateObject();
          $user->setIsActive(true);
          $user->setValidationKind(sfGuardUserTable::VALIDATION_KIND_NONE);
          $user->save();
          $widgets_connected = WidgetTable::getInstance()->updateByEmailToUser($user);
          $widgets_info = $widgets_connected ? $widgets_connected . ' existing widget(s) have been connected with your account.' : '';

          return $this->ajax()
              ->form($this->form)
              ->attr('#password_form input, #password_form button', 'disabled', 'disabled')
              ->scroll()
              ->alert('You have activated your account. You may login now. ' . $widgets_info, 'Congratulations!', '.page-header', 'after')->render();
        } else {
          return $this->ajax()->form($this->form)->render();
        }
      }
    }
  }

  public function executeDelete(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $user = sfGuardUserTable::getInstance()->find($id);
      /* @var $user sfGuardUser */
      if (!$user)
        return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('delete_user', $user->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#user_delete_modal .modal-body')->render();

      $user->delete();
      return $this->ajax()->redirectRotue('user_idx')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'delete', array('id' => $id, 'name' => $user->getFullName(), 'csrf_token' => $csrf_token))
        ->modal('#user_delete_modal')
        ->render();
  }

  public function executeBlock(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $user = sfGuardUserTable::getInstance()->find($id);
      /* @var $user sfGuardUser */
      if (!$user)
        return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('block_user', $user->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#user_block_modal .modal-body')->render();

      sfGuardUserPermissionTable::getInstance()->deleteUserPermission($user);
      $user->state(Doctrine_Record::STATE_DIRTY);
      $user->save();

      return $this->ajax()->redirectRotue('user_idx')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'block', array('id' => $id, 'name' => $user->getFullName(), 'csrf_token' => $csrf_token))
        ->modal('#user_block_modal')
        ->render();
  }

  public function executeUnblock(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $user = sfGuardUserTable::getInstance()->find($id);
      /* @var $user sfGuardUser */
      if (!$user)
        return $this->notFound();
    }

    $csrf_token = UtilCSRF::gen('unblock_user', $user->getId());

    if ($request->isMethod('post')) {
      if ($request->getPostParameter('csrf_token') != $csrf_token)
        return $this->ajax()->alert('CSRF Attack detected, please relogin.', 'Error', '#user_unblock_modal .modal-body')->render();

      if (!$user->hasPermission(myUser::CREDENTIAL_USER))
        $user->addPermissionByName(myUser::CREDENTIAL_USER);
      $user->state(Doctrine_Record::STATE_DIRTY);
      $user->save();

      return $this->ajax()->redirectRotue('user_idx')->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'unblock', array('id' => $id, 'name' => $user->getFullName(), 'csrf_token' => $csrf_token))
        ->modal('#user_unblock_modal')
        ->render();
  }

  public function executeSwitch(sfWebRequest $request) {
    $id = $request->getParameter('id');

    if (is_numeric($id)) {
      $user = sfGuardUserTable::getInstance()->find($id);
      /* @var $user sfGuardUser */
      if (!$user)
        return $this->notFound();

      if ($user->hasPermission(myUser::CREDENTIAL_ADMIN)) {
        return $this->ajax()->alert('You can not switch to an admin.')->render();
      }
      
      if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN)) { // check admin again (security.yml)
        return $this->ajax()->alert('You must be admin.')->render();
      }

      $this->getUser()->getAttributeHolder()->clear();
      $this->getUser()->signin($user);
      return $this->ajax()->redirectRotue('dashboard')->render();
    }
  }

  public function executeEmails(sfWebRequest $request) {
    $users = sfGuardUserTable::getInstance()->queryAll()->execute();
    $emails = array();
    foreach ($users as $user) { /* @var $user sfGuardUser */
      if ($user->hasValidEmail()) {
        $emails[] = $user->getEmailAddress();
      }
    }
    return $this->ajax()->appendPartial('body', 'emails', array('emails' => implode(', ', $emails)))->modal('#user_emails_modal')->render();
  }
}
