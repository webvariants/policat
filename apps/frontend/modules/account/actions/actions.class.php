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
 * account actions.
 *
 * @property Invitation $invitation
 *
 * @package    policat
 * @subpackage account
 * @author     Martin
 */
class accountActions extends policatActions {

  /**
   * Executes register action
   *
   * @param sfWebRequest $request A request object
   */
  public function executeRegister(sfWebRequest $request) {
    if (!StoreTable::getInstance()->getValueCached(StoreTable::REGISTER_ON))
      return $this->notFound();

    $this->invitation = InvitationTable::getInstance()->findByIdCode($request->getParameter('invitation'));

    $user = new sfGuardUser();

    $this->form = new RegisterForm($user);

    if ($request->getGetParameter('widgetval')) {
      $storage = sfContext::getInstance()->getStorage();
      if ($storage instanceof policatSessionStorage) {
        $storage->needSession();
      }
      $this->getUser()->setAttribute(myUser::SESSION_WIDGETVAL_ON, 1);
    }

    if ($request->isMethod('post')) {
      if (!$this->getUser()->human()) {
        return $this->ajax()->alert('Captcha missing. Please reload page.', 'Error', null, null, false, 'error')->render();
      }

      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $user->setIsActive(false);

        $user = $this->form->updateObject();
        $user->setUsername($user->getEmailAddress());

        $user->setValidationKind(sfGuardUserTable::VALIDATION_KIND_REGISTER_LINK);
        $user->randomValidationCode();

        $user->save();
        $user->addPermissionByName(myUser::CREDENTIAL_USER);

        $subject = 'validate register';
        $body = "#VALIDATION-URL#";

        $store = StoreTable::getInstance()->findByKeyAndLanguageWithFallback(StoreTable::REGISTER_MAIL, $user->getLanguageId());
        if ($store) {
          $subject = $store->getField('subject');
          $body = $store->getField('body');
        }

        $subst_escape = array(
            '#VALIDATION-URL#' => $this->generateUrl('register_validation', array('id' => $user->getId(), 'code' => $user->getValidationCode()), true),
            '#USER-NAME#' => $user->getFullName()
        );

        UtilMail::send('Register', 'User-' . $user->getId(), null, $user->getEmailAddress(), $subject, $body, null, null, $subst_escape, null, array(), true);

        $this->getUser()->setAttribute(myUser::SESSION_LAST_CAPTCHA, 0);

        if ($this->invitation) {
          $this->invitation->setRegisterUser($user);
          $this->invitation->save();
        }

        $mail = StoreTable::getInstance()->getValueCached(StoreTable::EMAIL_ADDRESS);
        return $this->ajax()
            ->form($this->form)
            ->attr('#register_form input, #register_form select, #register_form button', 'disabled', 'disabled')
            ->scroll()
            ->alert("To activate your user account, you have to verify your email address. "
              . "Look for the verification email in your inbox and click the link in the email. A confirmation "
              . "message will appear in your web browser. Didn't get the email? Check your spam folder to make "
              . "sure it didn't end up there. Add the email address $mail to your address book.", 'Account created.', '.register-success', 'append')->render();
      } else {
        return $this->ajax()->form($this->form)->render();
      }
    }

    $this->includeChosen();
  }

  public function executeRegisterValidation(sfWebRequest $request) {
    if (!StoreTable::getInstance()->getValueCached(StoreTable::REGISTER_ON))
      return $this->notFound();

    $id = $request->getParameter('id');
    $code = $request->getParameter('code');

    if ($this->getUser()->isAuthenticated() && $this->getGuardUser()->getId() == $id) {
      return $this->redirect($this->generateUrl('dashboard'));
    }

    $user = sfGuardUserTable::getInstance()->getByRegisterValidationByLink($id, $code, false);

    if ($user) {
      $user->setIsActive(true);
      $user->setValidationKind(sfGuardUserTable::VALIDATION_KIND_NONE);
      $user->save();
      $widgets_connected = WidgetTable::getInstance()->updateByEmailToUser($user);

      $invitation = $user->getInvitation();
      if ($invitation) {
        $invitation->applyToUser($user);
      }

      $this->user = $user;
      $this->widgets_connected = $widgets_connected;
    }
  }

  public function executeInvitation(sfWebRequest $request) {
    $this->invitation = InvitationTable::getInstance()->findByIdCode($request->getParameter('code'));

    $this->form = new BaseForm();
    $this->form->getWidgetSchema()->setNameFormat('transferinvitation[%s]');

    if ($request->isMethod('POST') && $this->invitation) {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $this->invitation->applyToUser($this->getGuardUser());
        $this->redirect('dashboard');
      } else {
        $this->redirect('homepage');
      }
    }
  }

  public function executeAjaxSignin($request) {
    /* @var $request sfWebRequest */
    if (!$request->isXmlHttpRequest())
      $this->redirect('@sf_guard_signin');

    $user = $this->getUser();
    if ($user->isAuthenticated())
      return $this->redirect('@homepage');

    $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin');
    $this->form = new $class();

    if ($request->isMethod('post')) {
      $this->form->bind($request->getParameter('signin'));
      if ($this->form->isValid()) {
        $values = $this->form->getValues();
        $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);
        if ($this->getUser()->getAttribute(myUser::SESSION_WIDGETVAL_ON) && $this->getUser()->getAttribute(myUser::SESSION_WIDGETVAL_IDCODE)) {
          $signinUrl = $this->generateUrl('widgetval', array('code' => $this->getUser()->getAttribute(myUser::SESSION_WIDGETVAL_IDCODE)));
          $this->getUser()->setAttribute(myUser::SESSION_WIDGETVAL_IDCODE, null);
          $this->getUser()->setAttribute(myUser::SESSION_WIDGETVAL_ON, null);
        }
        if (!isset($signinUrl)) {
          if (($request instanceof sfWebRequest) && ($request->getPostParameter('target'))) {
            if ($request->getPostParameter('target') == 'dashboard') {
              if ($this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN)) {
                $signinUrl = $this->generateUrl('admin', array(), true);
              } else {
                $signinUrl = $this->generateUrl('dashboard', array(), true);
              }
            }
          } elseif (($request instanceof sfWebRequest) && ($request->getPostParameter('href'))) {
            $signinUrl = $request->getPostParameter('href');
          } else {
            $signinUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url', $user->getReferer($request->getReferer()));
          }
        }
        return $this->ajax()->redirect($signinUrl ? $signinUrl : $this->generateUrl('homepage'))->render();
      } else {
//        return $this->dom()->form($this->form)->render();
        return
            $this->ajax()
            ->alert('Wrong user name or password. <a class="btn btn-mini" href="">Create new user account?</a>', 'Error', '#login_modal .modal-body', null, true)
            ->render();
      }
    } else {

      return $this->ajax()->appendComponent('body', 'account', 'ajaxSignin')->modal('#login_modal')->render();
    }
  }

  public function executeCaptcha(sfWebRequest $request) {
    $response = $request->getPostParameter('response');
    $ok = self::recaptcha_check_answer_version2($response);

    if ($ok) {
      $storage = sfContext::getInstance()->getStorage();
      if ($storage instanceof policatSessionStorage) {
        $storage->needSession();
      }
      $this->getUser()->setAttribute(myUser::SESSION_LAST_CAPTCHA, time());
      return $this->renderJson(array('success' => true));
    } else
      return $this->renderJson(array('success' => false));
  }

  protected static function recaptcha_check_answer_version2($response) {
    $privkey = sfConfig::get('app_recaptcha_secret');

    if ($response == null || !is_string($response) || strlen($response) == 0) {
      return false;
    }
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $url .= '?' . http_build_query(array(
          'secret' => $privkey,
          'response' => $response,
          'remoteip' => $_SERVER["REMOTE_ADDR"]
        ), null, '&');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    $curlData = curl_exec($curl);

    curl_close($curl);

    $res = json_decode($curlData, TRUE);

    return is_array($res) && $res['success'] === true;
  }

  public function executeProfile(sfWebRequest $request) {
    $user = $this->getGuardUser();
    $this->form = new ProfileForm($user);

    if ($request->isMethod('post')) {
      $this->form->bind($request->getPostParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $user = $this->form->updateObject();
        $user->setUsername($user->getEmailAddress());
        $user->save();

        return $this->ajax()->redirectRotue('dashboard')->render();
      } else {
        return $this->ajax()->form($this->form)->render();
      }
    }

    $this->includeChosen();
  }

  public function executeUnblock(sfWebRequest $request) {
    if ($this->getUser()->isNotBlocked())
      return $this->ajax()->redirectRotue('dashboard')->render();

    if (TicketTable::getInstance()->checkOpenUnblockTicketForUser($this->getGuardUser()))
      return $this->ajax()->alert('There is already a pending ticket for your request.', 'Sorry.')->render();

    $form = new UnblockForm();

    if ($request->isMethod('post')) {
      $form->bind($request->getParameter($form->getName()));
      if ($form->isValid()) {
        $ticket = TicketTable::getInstance()->generate(array(
            TicketTable::CREATE_AUTO_FROM => true,
            TicketTable::CREATE_CHECK_DUPLICATE => true,
            TicketTable::CREATE_KIND => TicketTable::KIND_USER_UNBLOCK,
            TicketTable::CREATE_TEXT => $form->getValue('reason')
        ));

        if ($ticket) {
          $ticket->save();

          return $this->ajax()
              ->modal('#unblock_modal', 'hide')
              ->remove('#unblock_modal')
              ->alert('Message sent.')
              ->render();
        } else {
          return $this->ajax()->alert('There is already a pending ticket for your request.', 'Sorry')->render();
        }
      } else
        return $this->ajax()->form($form)->render();
    }

    return $this->ajax()
        ->appendPartial('body', 'unblock', array('form' => $form))
        ->modal('#unblock_modal')
        ->render();
  }

  public function executeForgotten($request) {
    /* @var $request sfWebRequest */
    if (!$request->isXmlHttpRequest())
      $this->redirect404();

    $user = $this->getUser();
    if ($user->isAuthenticated())
      return $this->redirect('@homepage');

    $form = new sfGuardRequestForgotPasswordForm();

    if ($request->isMethod('post')) {
      $form->bind($request->getParameter($form->getName()));
      if ($form->isValid()) {
//        $this->user = $form->user;
        $this->_deleteOldUserForgotPasswordRecords($form->user->id);

        $salt = '';
        while (strlen($salt) < 16) {
          $salt .= base_convert(mt_rand(), 10, 36);
        }
        $salt = substr($salt, 0, 16);
        $code = base_convert(sha1('policat' . mt_rand() . microtime() . mt_rand() . mt_rand()), 16, 36);

        $forgotPassword = new sfGuardForgotPassword();
        $forgotPassword->user_id = $form->user->id;
        $forgotPassword->unique_key = crypt($code, '$6$' . $salt);
        $forgotPassword->expires_at = new Doctrine_Expression('NOW()');
        $forgotPassword->save();

//        $message = Swift_Message::newInstance()
//          ->setFrom(sfConfig::get('app_sf_guard_plugin_default_from_email', 'from@noreply.com'))
//          ->setTo($this->form->user->email_address)
//          ->setSubject('Forgot Password Request for ' . $this->form->user->username)
//          ->setBody($this->getPartial('sfGuardForgotPassword/send_request', array('user' => $this->form->user, 'forgot_password' => $forgotPassword)))
//          ->setContentType('text/html')
//        ;
//
//        $this->getMailer()->send($message);

        $subject = 'password forgotten';
        $body = "#VALIDATION-URL#";

        $store = StoreTable::getInstance()->findByKeyAndLanguageWithFallback(StoreTable::PASSWORD_FORGOTTEN_MAIL, $form->user->getLanguageId());
        if ($store) {
          $subject = $store->getField('subject');
          $body = $store->getField('body');
        }

        $subst_escape = array(
            '#VALIDATION-URL#' => $this->generateUrl('password_reset', array('id' => $form->user->getId(), 'code' => $code), true),
            '#USER-NAME#' => $form->user->getFullName()
        );

        UtilMail::send('Password', 'User-' . $form->user->getId(), null, $form->user->getEmailAddress(), $subject, $body, null, null, $subst_escape, null, array(), true);

        return
            $this->ajax()
            ->remove('#forgotten_modal .alert')
            ->attr('#forgotten_modal input, #forgotten_modal button', 'disabled', 'disabled')
            ->alert('Request accepted. Check your mail now.', '', '#forgotten_modal .modal-body', 'append')
            ->render();
      } else {
        return
            $this->ajax()
            ->remove('#forgotten_modal .alert')
            ->alert('Wrong e-mail address.', 'Error', '#forgotten_modal .modal-body', 'append')
            ->render();
      }
    } else {

      return $this->ajax()
          ->remove('#forgotten_modal')
          ->appendPartial('body', 'forgotten', array('form' => $form))
          ->modal('#login_modal', 'hide')
          ->modal('#forgotten_modal')
          ->render();
    }
  }

  public function executeReset(sfWebRequest $request) {
    if (!StoreTable::getInstance()->getValueCached(StoreTable::REGISTER_ON))
      return $this->notFound();

    $id = $request->getParameter('id');
    $code = $request->getParameter('code');

    if ($this->getUser()->isAuthenticated() && $this->getGuardUser()->getId() == $id) {
      return $this->redirect($this->generateUrl('dashboard'));
    }

    $user = sfGuardUserTable::getInstance()->getByPasswordForgottenCode($id, $code);

    if ($user) {
      $this->user = $user;
      $form = new sfGuardChangeUserPasswordForm($user);
      $this->form = $form;

      if ($request->isMethod('post')) {
        $form->bind($request->getParameter($form->getName()));
        if ($form->isValid()) {
          $this->_deleteOldUserForgotPasswordRecords($user->getId());
          $form->save();
          $login_url = $this->generateUrl('ajax_signin');
          return $this->ajax()
              ->form($form)
              ->attr('#reset_form input, #reset_form button', 'disabled', 'disabled')
              ->alert('Password changed. You may <a class="ajax_link" href="' . $login_url . '">login</a> now', '', '#reset_form .form-actions', 'before', true)
              ->render();
        } else {
          return $this->ajax()->form($form)->render();
        }
      }
    }
  }

  private function _deleteOldUserForgotPasswordRecords($id) {
    Doctrine_Core::getTable('sfGuardForgotPassword')
      ->createQuery('p')
      ->delete()
      ->where('p.user_id = ?', $id)
      ->execute();
  }

}
