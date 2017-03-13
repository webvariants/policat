<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

require_once(dirname(__FILE__) . '/../../../../../plugins/sfDoctrineGuardPlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions.class.php');

class sfGuardAuthActions extends BasesfGuardAuthActions {

  public function preExecute() {
    parent::preExecute();

    policatActions::preExecuteCacheHeaders($this->getRequest(), $this->getResponse(), $this->getUser(), $this->isSecure());
  }

  protected function showLogin() {
    $ajax = new Ajax($this);
    $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin');
    $this->form = new $class();
    return $ajax
        ->appendComponent('body', 'account', 'ajaxSignin')
        ->modal('#login_modal')
        ->alert('Please signin.', 'Session timeout.', '#login_modal .modal-body', 'prepend')
        ->render();
  }

  public function executeSignin($request) {
    if ($request instanceof sfWebRequest && $request->isXmlHttpRequest())
      return $this->showLogin();

    return parent::executeSignin($request);
  }

  public function executeSecure($request) {
    if ($request instanceof sfWebRequest && $request->isXmlHttpRequest())
      return $this->showLogin();

    return parent::executeSecure($request);
  }

  public function executeSignout($request) {
    $this->getUser()->signOut();
    $this->redirect('@homepage');
  }

}
