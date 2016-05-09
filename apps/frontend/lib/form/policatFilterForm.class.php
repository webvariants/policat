<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

abstract class policatFilterForm extends BaseForm {

  const NAME = '_';

  public function setup() {
    $this->widgetSchema->setFormFormatterName('bootstrapSpan');
    $this->widgetSchema->setNameFormat(self::NAME . '[%s]');
    $this->disableLocalCSRFProtection();
  }

  abstract function filter(Doctrine_Query $query);

  protected $_query_params_cache = null;

  public function getQueryParams() {
    if ($this->_query_params_cache !== null)
      return $this->_query_params_cache;

    $query_params = array();

    foreach ($this->getValues() as $key => $value) {
      if ($value instanceof Doctrine_Record) {
        $query_params[$key] = $value->getId();
      } elseif ($value instanceof Doctrine_Collection || is_array($value)) {
        $values = array();
        foreach ($value as $record) {
          if ($record instanceof Doctrine_Record)
            $values[] = $record->getId();
          elseif (is_scalar($record))
            $values[] = $record;
        }
        if ($values)
          $query_params[$key] = $values;
      }
      elseif (is_scalar($value)) {
        $query_params[$key] = $value;
      }
    }

    return $this->_query_params_cache = array(self::NAME => $query_params);
  }

  public function bindSelf($session_key = null) {
    $request = sfContext::getInstance()->getRequest();
    if ($session_key !== null) {
      $session_key .= '_' . get_class($this);
    }
    if ($request->hasParameter($this->getName())) {
      $this->bind($request->getParameter($this->getName()));

      if ($session_key && $this->isValid()) {
        $qp = $this->getQueryParams();
        sfContext::getInstance()
          ->getUser()
          ->setAttribute($session_key, $this->isValid() ? $qp[$this->getName()] : null, get_class($this));
      }
    } elseif ($session_key) {
      $this->bind(sfContext::getInstance()->getUser()->getAttribute($session_key, array(), get_class($this)));
    } else {
      $this->bind(array());
    }
  }

}