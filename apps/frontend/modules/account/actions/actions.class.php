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
      if (!$this->getUser()->human())
        return $this->captchaModal();

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

        return $this->ajax()
            ->form($this->form)
            ->attr('#register_form input, #register_form select, #register_form button', 'disabled', 'disabled')
            ->scroll()
            ->alert('Congratulations! You have created a new account. For your first login, you need to check your inbox '
              . 'and click the account validation link in the e-mail we have sent to you.'
              , 'Please check your inbox now!', '.page-header', 'after')->render();
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

      $this->user = $user;
      $this->widgets_connected = $widgets_connected;
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
              $signinUrl = $this->generateUrl('dashboard', array(), true);
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
            ->alert('Wrong user name or password.', 'Error', '#login_modal .modal-body')
            ->render();
      }
    } else {

      return $this->ajax()->appendPartial('body', 'ajaxSignin')->modal('#login_modal')->render();
    }
  }

  public function executeCaptcha(sfWebRequest $request) {
    $challenge = $request->getPostParameter('challenge');
    $response = $request->getPostParameter('response');

    $ok = self::recaptcha_check_answer($challenge, $response);
    if ($ok) {
      $storage = sfContext::getInstance()->getStorage();
      if ($storage instanceof policatSessionStorage) {
        $storage->needSession();
      }
      $this->getUser()->setAttribute(myUser::SESSION_LAST_CAPTCHA, time());
      return $this->ajax()->j('addClass', 'body', 'captcha_ok')->render();
    } else
      return $this->ajax()->render();
  }

  private static function recaptcha_http_post($host, $path, $data, $port = 80) {
    $req = "";
    foreach ($data as $key => $value)
      $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
    $req = substr($req, 0, strlen($req) - 1);

    $http_request = "POST $path HTTP/1.0\r\n";
    $http_request .= "Host: $host\r\n";
    $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
    $http_request .= "Content-Length: " . strlen($req) . "\r\n";
    $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
    $http_request .= "\r\n";
    $http_request .= $req;

    $response = '';
    if (false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ))
      die('Could not open socket');

    fwrite($fs, $http_request);

    while (!feof($fs))
      $response .= fgets($fs, 1160);
    fclose($fs);
    $response = explode("\r\n\r\n", $response, 2);

    return $response;
  }

  protected static function recaptcha_check_answer($challenge, $response, $extra_params = array()) {
    $privkey = sfConfig::get('app_recaptcha_secret');
    $remoteip = $_SERVER["REMOTE_ADDR"];

    if ($challenge == null || !is_string($challenge) || !is_string($response) || strlen($challenge) == 0 || $response == null || strlen($response) == 0)
      return false;

    $response = self::recaptcha_http_post('www.google.com', "/recaptcha/api/verify", array(
          'privatekey' => $privkey,
          'remoteip' => $remoteip,
          'challenge' => $challenge,
          'response' => $response
        ) + $extra_params
    );

    $answers = explode("\n", $response [1]);

    return trim(reset($answers)) == 'true';
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
