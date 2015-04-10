<?php

class myUser extends sfGuardSecurityUser {

  const CREDENTIAL_SYSTEM = 'system';
  const CREDENTIAL_ADMIN = 'admin';
  const CREDENTIAL_USER = 'user';
  const SESSION_WIDGETVAL_IDCODE = 'widgetval_idcode';
  const SESSION_WIDGETVAL_ON = 'widgetval_on';
  const SESSION_LAST_PERMISSION_CHECK = 'last_perm_check';
  const SESSION_LAST_ATHENTICATED_CHECK = 'last_auth_check';
  const SESSION_LAST_CAPTCHA = 'last_captcha';

  /**
   * User is human when authorized or captcha was resolved 15 minutes ago.
   * 
   * @return boolean 
   */
  public function human() {
    if ($this->isAuthenticated())
      return true;
    $last_captcha = $this->getAttribute(self::SESSION_LAST_CAPTCHA);

    return $last_captcha && ($last_captcha + 60 * 5 > time());
  }

  public function getUserId() {
    return $this->getGuardUser()->getId();
  }

  public function getFirstName() {
    return $this->getGuardUser()->getFirstName();
  }

  public function isNotBlocked() {
    return $this->isAuthenticated() && ($this->hasCredential(array(
          self::CREDENTIAL_USER,
          self::CREDENTIAL_ADMIN,
          self::CREDENTIAL_SYSTEM
        ), false));
  }

  public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array()) {
    parent::initialize($dispatcher, $storage, $options);

    if ($this->user === null && $storage instanceof policatSessionStorage && !$this->human() && !$this->getAttribute(self::SESSION_WIDGETVAL_ON)) {
      // if user is not logged in or session is needed for other stuff forget the session cookie for better caching
      
      $request = sfContext::getInstance()->getRequest();
      if ($request instanceof sfWebRequest) {
        if ($request->isMethod('post')) {
          return; // never drop session on POST request
        }
      }

      $storage->dropSession();
    }
  }

  public function signIn($user, $remember = false, $con = null) {
    $storage = sfContext::getInstance()->getStorage();
    if ($storage instanceof policatSessionStorage) {
      $storage->needSession();
    }

    parent::signIn($user, $remember, $con);

    if ($user instanceof sfGuardUser) {
      $this->setAttribute(self::SESSION_LAST_ATHENTICATED_CHECK, $user->getUpdatedAt());
      $this->setAttribute(self::SESSION_LAST_PERMISSION_CHECK, $user->getUpdatedAt());
    }
  }

  public function isAuthenticated() {
    if ($this->getGuardUser()) {
      if ($this->hasAttribute(self::SESSION_LAST_ATHENTICATED_CHECK)) {
        if ($this->getAttribute(self::SESSION_LAST_ATHENTICATED_CHECK) != $this->getGuardUser()->getUpdatedAt()) {
          if (!$this->getGuardUser()->getIsActive()) {
            $this->signOut();
            return false;
          }
          $this->setAttribute(self::SESSION_LAST_ATHENTICATED_CHECK, $this->getGuardUser()->getUpdatedAt());
        }
      } else {
        $this->setAttribute(self::SESSION_LAST_ATHENTICATED_CHECK, $this->getGuardUser()->getUpdatedAt());
      }
    }

    return parent::isAuthenticated();
  }

  public function hasCredential($credential, $useAnd = true) {
    if ($this->isAuthenticated() && $this->getGuardUser()) {
      if ($this->hasAttribute(self::SESSION_LAST_PERMISSION_CHECK)) {
        if ($this->getAttribute(self::SESSION_LAST_PERMISSION_CHECK) != $this->getGuardUser()->getUpdatedAt()) {
          $this->setAuthenticated(true);
          $this->clearCredentials();
          $this->addCredentials($this->getGuardUser()->getAllPermissionNames());
          $this->setAttribute(self::SESSION_LAST_PERMISSION_CHECK, $this->getGuardUser()->getUpdatedAt());
        }
      } else {
        $this->setAttribute(self::SESSION_LAST_PERMISSION_CHECK, $this->getGuardUser()->getUpdatedAt());
      }
    }

    return parent::hasCredential($credential, $useAnd);
  }

  public function getReferer($default) {
    if ($default)
      return $default;
    return parent::getReferer($default);
  }

}
