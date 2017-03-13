<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class Ajax {

  protected $data = array();

  /**
   * @var sfAction 
   */
  protected $action = null;

  public function __construct(sfAction $action) {
    $this->action = $action;
  }

  public function add($cmd, $data = null) {
    $this->data[] = array('cmd' => $cmd, 'data' => $data);
    return $this;
  }

  public function jSelect($cmd, $selector, $args = array()) {
    return $this->add('j', array('cmd' => $cmd, 'selector' => $selector, 'args' => $args));
  }
  
  public function j0($cmd, $selector) {
    return $this->jSelect($cmd, $selector, array());
  }

  public function j($cmd, $selector, $arg) {
    return $this->jSelect($cmd, $selector, array($arg));
  }

  public function jPartial($cmd, $selector, $templateName, $vars = null) {
    return $this->j($cmd, $selector, $this->action->getPartial($templateName, $vars));
  }

  public function jComponent($cmd, $selector, $moduleName, $componentName, $vars = null) {
    return $this->j($cmd, $selector, $this->action->getComponent($moduleName, $componentName, $vars));
  }

  public function focus($selector) {
    return $this->jSelect('focus', $selector);
  }

  public function hide($selector) {
    return $this->jSelect('hide', $selector);
  }

  public function show($selector) {
    return $this->jSelect('show', $selector);
  }

  public function tooltip($selector) {
    return $this->jSelect('tooltip', $selector);
  }

  public function html($selector, $markup) {
    return $this->j('html', $selector, $markup);
  }

  public function htmlPartial($selector, $templateName, $vars = null) {
    return $this->jPartial('html', $selector, $templateName, $vars);
  }

  public function remove($selector) {
    return $this->jSelect('remove', $selector);
  }
  
  public function append($selector, $markup) {
    return $this->j('append', $selector, $markup);
  }

  public function appendPartial($selector, $templateName, $vars = null) {
    return $this->jPartial('append', $selector, $templateName, $vars);
  }

  public function appendComponent($selector, $moduleName, $componentName, $vars = null) {
    return $this->jComponent('append', $selector, $moduleName, $componentName, $vars);
  }
  
  public function afterPartial($selector, $templateName, $vars = null) {
    return $this->jPartial('after', $selector, $templateName, $vars);
  }

  public function replaceWith($selector, $markup) {
    return $this->j('replaceWith', $selector, $markup);
  }

  public function replaceWithPartial($selector, $templateName, $vars = null) {
    return $this->jPartial('replaceWith', $selector, $templateName, $vars);
  }

  public function replaceWithComponent($selector, $moduleName, $componentName, $vars = null) {
    return $this->jComponent('replaceWith', $selector, $moduleName, $componentName, $vars);
  }

  public function attr($selector, $name, $value) {
    return $this->jSelect('attr', $selector, array($name, $value));
  }

  public function addClass($selector, $class) {
    return $this->j('addClass', $selector, $class);
  }

  public function removeClass($selector, $class) {
    return $this->j('removeClass', $selector, $class);
  }
  
  public function val($selector, $value) {
    return $this->j('val', $selector, $value);
  }
  
  public function trigger($selector, $value) {
    return $this->j('trigger', $selector, $value);
  }
  
  public function empty_($selector) {
    return $this->j0('empty', $selector);
  }

  public function chosen($selector, $options = array()) {
    return $this->j('chosen', $selector, $options);
  }

  public function select2($selector, $options = array()) {
    return $this->j('select2', $selector, $options);
  }

  public function select2color($selector, $options = array()) {
    return $this->j('select2color', $selector, $options);
  }

  protected $alert_selector = 'header';
  protected $alert_action = 'after';

  public function alert($message = '', $heading = 'Info', $selector = null, $action = null, $raw = false, $type = null) {
    if ($selector === null)
      $selector = $this->alert_selector;
    if ($action === null)
      $action = $this->alert_action;

    return $this->jPartial($action, $selector, 'dashboard/alert', array(
          'heading' => $heading,
          'message' => $message,
          'raw' => $raw,
          'type' => $type
      ));
  }

  public function setAlertTarget($selector = null, $action = null) {
    if ($selector)
      $this->alert_selector = $selector;
    if ($action)
      $this->alert_action = $action;
  }

  public function modal($selector, $options = array()) {
    return $this->j('modal', $selector, $options);
  }

  public function scroll($pos = 0) {
    return $this->add('scroll', $pos);
  }

  public function redirect($url, $force_reload = false) {
    return $this->add('redirect', array('url' => $url, 'reload' => $force_reload));
  }

  public function redirectRotue($route, $params = array(), $get_param = array(), $force_reload = false) {
    return $this->redirect($this->action->generateUrl($route, $params, true) . ($get_param ? '?' . http_build_query($get_param, null, '&') : ''), $force_reload);
  }

  public function redirectPost($url, $data = array()) {
    return $this->add('redirect-post', array('url' => $url, 'data' => $data));
  }

  public function redirectPostRoute($route, $params = array(), $data = array()) {
    return $this->redirectPost($this->action->generateUrl($route, $params), $data);
  }

  public function initRecaptcha() {
    return $this->add('initRecaptcha');
  }
  
  public function click($selector) {
    return $this->j0('click', $selector);
  }

  public function render($iframe_transport = false) {
    sfConfig::set('sf_web_debug', false);
    if ($iframe_transport)
      return $this->action->renderText('<textarea data-type="application/json">' . $this->json() . '</textarea>');
    else
      return $this->action->renderText($this->json());
  }

  public function json() {
    return json_encode($this->data);
  }

  public function __toString() {
    return '   Do not forget to call render()!   ';
  }
  
  public function edits($selector = null) {
    return $this->add('edits', $selector);
  }

  public function form(sfForm $form) {
    return $this->add('form', array(
          'form_errors' => self::errorSchema2Array($form, $form->getErrorSchema()),
          'form_prefix' => $form->getName() . '_'
      ));
  }

  public function debugFormErrors(sfForm $form, $selector = null, $action = 'append') {
    return $this->alert(json_encode(self::errorSchema2ArrayLabel($form, $form->getErrorSchema())), 'Form-Errors', $selector, $action);
  }

  public function form_error_list(sfForm $form, $selector = null, $action = 'append') {
    $errors = self::errorSchema2ArrayLabel($form, $form->getErrorSchema());
    if (!$errors)
      return $this->alert('no errors', '', $selector, $action, true);

    if (count($errors) == 1)
      foreach ($errors as $key => $value)
        return $this->alert('<strong>' . htmlentities($key, ENT_COMPAT, 'utf-8') . ' </strong>' . htmlentities(implode(', ', $value), ENT_COMPAT, 'utf-8'), '', $selector, $action, true);

    $ret = '<ul>';
    foreach ($errors as $key => $value) {
      $ret .= '<li><strong>' . htmlentities($key, ENT_COMPAT, 'utf-8') . ' </strong>' . htmlentities(implode(', ', $value), ENT_COMPAT, 'utf-8') . '</li>';
    };
    $ret .= '<ul>';

    return $this->alert($ret, '', $selector, $action, true);
  }

  /**
   * @param sfForm $form
   * @param sfValidatorErrorSchema $errorSchema
   * @return array
   */
  protected static function errorSchema2Array($form, $errorSchema, $prefix = '') {
    $formatter = $form->getWidgetSchema()->getFormFormatter();
    $errors = array();
    foreach ($errorSchema->getErrors() as $key => $error) {
      /* @var $error sfValidatorError */
      if ($error instanceof sfValidatorErrorSchema) {
        $errors = $errors + self::errorSchema2Array($form, $error, $prefix . $key . '_');
      } else {
        $errors[$prefix . $key] = $formatter->translate($error->getMessageFormat(), $error->getArguments());
      }
    }
    return $errors;
  }

  /**
   * @param sfForm $form
   * @param sfValidatorErrorSchema $errorSchema
   * @return array
   */
  protected static function errorSchema2ArrayLabel($form, $errorSchema, $errors = array(), $default_label = null) {
    foreach ($errorSchema->getErrors() as $key => $error) {
      /* @var $error sfValidatorError */
      $label = $default_label ? $default_label : self::getLabel($form, $key);
      if ($error instanceof sfValidatorErrorSchema)
        $errors = self::errorSchema2ArrayLabel($form, $error, $errors, $label);
      else
        $errors[$label][] = $form->getWidgetSchema()->getFormFormatter()->translate($error->getMessageFormat(), $error->getArguments());
    }
    return $errors;
  }

  protected static function getLabel(sfForm $form, $name) {
    try {
      $widget = $form->getWidget($name);
    } catch (Exception $e) {
      $widget = null;
    }

    if ($widget) {
      $label = $widget->getLabel();
      if ($label)
        return $label;

      $placeholder = $widget->getAttribute('placeholder');
      if ($placeholder)
        return $placeholder;
    }

    return $name;
  }

}