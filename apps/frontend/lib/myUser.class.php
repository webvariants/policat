<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

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

  private $__cache_link_campaign = array();

  public function linkCampaign($campaign, $maxlen = null, $text = null, $css_class = '', $hide_if_no_link = false) {
    $guardUser = $this->getGuardUser();

    if (is_object($campaign) && $campaign instanceof sfOutputEscaperIteratorDecorator) {
      $campaign = $campaign->getRawValue();
      /* @var $campaign Campaign */
    }

    if (!$guardUser || !$campaign) {
      return '';
    }

    $context = sfContext::getInstance();

    $name = $campaign->getName();
    if ($text) {
      $name = $text;
    }

    if ($maxlen) {
      $context->getConfiguration()->loadHelpers('Text');
      $short = truncate_text($name, $maxlen, '…');
    } else {
      $short = $name;
    }

    if (!array_key_exists($campaign->getId(), $this->__cache_link_campaign)) {
      $this->__cache_link_campaign[$campaign->getId()] = $guardUser->hasPermission(myUser::CREDENTIAL_ADMIN) || $guardUser->isCampaignMember($campaign);
    }

    if ($this->__cache_link_campaign[$campaign->getId()]) {
      $link = $context->getRouting()->generate('campaign_edit_', array('id' => $campaign->getId()));
    } else {
      $link = null;
    }

    if ($link) {
      printf('<a class="%s" href="%s" title="%s">%s</a>', $css_class, $link, $short !== $name ? Util::enc($name) : '', Util::enc($short));
    } else {
      if (!$hide_if_no_link) {
        printf('<span class="%s" title="%s">%s</span>', $css_class, $short !== $name ? Util::enc($name) : '', Util::enc($short));
      }
    }
  }

  private $__cache_link_petition = array();

  public function linkPetition($petition, $maxlen = null, $text = null, $css_class = '', $hide_if_no_link = false, $route = 'petition_overview') {
    $guardUser = $this->getGuardUser();

    if (is_object($petition) && $petition instanceof sfOutputEscaperIteratorDecorator) {
      $petition = $petition->getRawValue();
      /* @var $petition Petition */
    }

    if (!$guardUser || !$petition) {
      return '';
    }

    $context = sfContext::getInstance();

    $name = $petition->getName();
    if ($text) {
      $name = $text;
    }

    if ($maxlen) {
      $context->getConfiguration()->loadHelpers('Text');
      $short = truncate_text($name, $maxlen, '…');
    } else {
      $short = $name;
    }

    if (!array_key_exists($petition->getId(), $this->__cache_link_petition)) {
      $this->__cache_link_petition[$petition->getId()] = $guardUser->hasPermission(myUser::CREDENTIAL_ADMIN) || $guardUser->isPetitionMember($petition, true);
    }

    if ($this->__cache_link_petition[$petition->getId()]) {
      $link = $context->getRouting()->generate($route, array('id' => $petition->getId()));
    } else {
      $link = null;
    }

    if ($link) {
      printf('<a class="%s" href="%s" title="%s">%s</a>', $css_class, $link, $short !== $name ? Util::enc($name) : '', Util::enc($short));
    } else {
      if (!$hide_if_no_link) {
        printf('<span class="%s" title="%s">%s</span>', $css_class, $short !== $name ? Util::enc($name) : '', Util::enc($short));
      }
    }
  }

  private $__cache_link_widget = array();

  public function linkWidget($widget, $text = null, $css_class = '', $hide_if_no_link = false) {
    $guardUser = $this->getGuardUser();

    if (is_object($widget) && $widget instanceof sfOutputEscaperIteratorDecorator) {
      $widget = $widget->getRawValue();
      /* @var $widget Petition */
    }

    if (!$guardUser || !$widget) {
      return '';
    }

    $context = sfContext::getInstance();

    $name = $widget->getId();
    if (is_string($text)) {
      $name = $text;
    }

    if (!array_key_exists($widget->getId(), $this->__cache_link_widget)) {
      $this->__cache_link_widget[$widget->getId()] = $guardUser->getId() == $widget->getUserId() || $guardUser->hasPermission(myUser::CREDENTIAL_ADMIN) || $guardUser->isPetitionMember($widget->getPetition());
    }

    if ($this->__cache_link_widget[$widget->getId()]) {
      $link = $context->getRouting()->generate('widget_edit', array('id' => $widget->getId()));
    } else {
      $link = null;
    }

    if ($link) {
      printf('<a class="%s" href="%s">%s</a>', $css_class, $link, Util::enc($name));
    } else {
      if (!$hide_if_no_link) {
        printf('<span class="%s">%s</span>', $css_class, Util::enc($name));
      }
    }
  }

}
