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
 * @method myUser   getUser()          get the User Object
 * @method null     setContentTags(mixed $tags)
 * @method null     addContentTags(mixed $tags)
 * @method array    getContentTags()
 * @method null     removeContentTags()
 * @method null     setContentTag(string $tagName, string $tagVersion)
 * @method boolean  hasContentTag(string $tagName)
 * @method null     removeContentTag(string $tagName)
 */
class policatActions extends sfActions {

  public static function preExecuteCacheHeaders($request, $response, $user, $is_secure) {
    if ($request instanceof sfWebRequest && $response instanceof sfWebResponse) {
      if (($request->isMethod('GET') || $request->isMethod('HEAD')) && !$request->isXmlHttpRequest()) {
        if (sfContext::getInstance()->getConfiguration()->getApplication() === 'frontend') {
          $response->addVaryHttpHeader('Cookie');
        }

        if (!$user instanceof sfGuardSecurityUser || $user->isAnonymous()) {
          $response->addCacheControlHttpHeader('public');
          $response->addCacheControlHttpHeader('must-revalidate');
          $response->addCacheControlHttpHeader('max_age=60');
        } else {
          $response->addVaryHttpHeader('Cookie');
          $response->addCacheControlHttpHeader('private');
          $response->addCacheControlHttpHeader('no-store');
          $response->addCacheControlHttpHeader('max_age=0');
        }
      } else {
        $response->addCacheControlHttpHeader('private');
        $response->addCacheControlHttpHeader('no-store');
        $response->addCacheControlHttpHeader('max_age=0');
      }
    }
  }

  public function preExecute() {
    parent::preExecute();

    self::preExecuteCacheHeaders($this->getRequest(), $this->getResponse(), $this->getUser(), $this->isSecure());
  }

  /**
   *
   * @return sfGuardUser
   */
  public function getGuardUser() {
    return $this->getUser()->getGuardUser();
  }

  public function userIsAdmin() {
    return $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
  }

  /**
   *
   * @param sfGuardUser $user or User ID
   * @return boolean
   * @throws Exception
   */
  public function isSelfUser($user) {
    if ($user instanceof sfGuardUser) {
      return $this->getGuardUser()->getId() == $user->getId();
    } elseif (is_numeric($user))
      return $user == $this->getGuardUser()->getId();

    throw new Exception('wrong argument');
  }

  public function captchaModal() {
    return $this->ajax()->appendPartial('body', 'account/captchaModal')->modal('#captcha_modal')->initRecaptcha()->render();
  }

  /**
   *
   * @return sfCache
   */
  protected function getCache() {
    $cache = $this->getContext()->getViewCacheManager()->getCache();
    if ($cache instanceof sfTagCache)
      $cache = $cache->getCache();
    return $cache;
  }

  protected function setPageTags($tags) {
    return $this->getContext()->getViewCacheManager()->setPageTags($tags);
  }

  protected function handleForm(sfFormDoctrine $form, $uri = null, $action = null, $params = null) {
    if ($this->getRequest()->isMethod('post')) {
      $request = $this->getRequest();
      $formname = $form->getName();
      $form->bind($request->getPostParameter($formname), $form->isMultipart() ? $request->getFiles($formname) : null);
      if ($form->isValid()) {
        $form->save();
        if (is_array($action))
          $params = $action;
        if (is_array($params)) {
          $url = $this->getContext()->getRouting()->generate($uri . (is_string($action) ? '/' . $action : ''), $params, true);
          $this->redirect($url);
        }
        if (is_string($uri)) {
          if ($action === null)
            $this->redirect($uri);
          else
            $this->forward($uri, $action);
        }
      }
    }
  }

  public function renderJson(array $data, $callback = null) {
    $response = $this->getResponse();

    if ($callback) {
      $response->setContentType('application/javascript');
      // prepend callback with an empty comment to prevent CVE-2014-4671
      // http://miki.it/blog/2014/7/8/abusing-jsonp-with-rosetta-flash/
      $response->setContent('/**/' . $callback . '(' . json_encode($data) . ');');
    } else {
      $response->setContentType('application/json');
      $response->setContent(json_encode($data));
    }

    return sfView::NONE;
  }

  private $ajax_instance = null;

  /**
   * @return Ajax
   */
  public function ajax() {
    if ($this->ajax_instance !== null)
      return $this->ajax_instance;
    return $this->ajax_instance = new Ajax($this);
  }

  public function notFound($message = 'Not found.') {
    $request = $this->getRequest();
    if ($request instanceof sfWebRequest && $request->isXmlHttpRequest())
      return $this->ajax()->alert($message)->render();
    $this->message = $message;
    return $this->forward404($message);
  }

  public function noAccess($message = 'Access denied.', $heading = 'Access denied') {
    $request = $this->getRequest();
    if ($request instanceof sfWebRequest && $request->isXmlHttpRequest())
      return $this->ajax()->alert($message, $heading)->render();
    $this->message = $message;
    $this->heading = $heading;
    $this->getResponse()->setStatusCode(403);
    $this->setLayout('dashboard');
    $this->setTemplate('secure', 'sfGuardAuth');
  }

  public function includeMarkdown() {
    $response = $this->getResponse();
    if ($response instanceof sfWebResponse) {
      $response->addStylesheet('dist/markitup/skins/markitup/style.css', 'last');
      $response->addStylesheet('markdown/style.css', 'last');
      $response->addJavascript('dist/showdown.js', 'last');
      $response->addJavascript('dist/markitup/jquery.markitup.js', 'last');
      $response->addJavascript('markdown/set.js', 'last');
    }
  }

  public function includeIframeTransport() {
    $response = $this->getResponse();
    if ($response instanceof sfWebResponse) {
      $response->addJavascript('dist/jquery.iframe-transport.js', 'last');
    }
  }

  public function includeHighlight() {
    $response = $this->getResponse();
    if ($response instanceof sfWebResponse) {
      $response->addStylesheet('dist/jquery.highlighttextarea.css', 'last');
      $response->addJavascript('dist/jquery.highlighttextarea.js', 'last');
    }
  }

  public function includeChosen() {
    $response = $this->getResponse();
    if ($response instanceof sfWebResponse) {
      $response->addStylesheet('dist/chosen.min.css', 'last');
      $response->addJavascript('dist/chosen.jquery.min.js', 'last');
    }
  }

}
