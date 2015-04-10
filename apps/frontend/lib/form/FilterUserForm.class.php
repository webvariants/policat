<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class FilterUserForm extends policatFilterForm {

  public function configure() {
    $this->setWidget(ContactTable::FILTER_SEARCH, new sfWidgetFormInputText(array(
        'label' => false
      ), array(
        'type' => 'search',
        'placeholder' => 'Search',
        'class' => 'span2',
        'style' => 'vertical-align:top',
        'title' => 'Enter a part of, or the full name or email-address. If you don\'t get a search result, check different spellings and accents'
    )));
    $this->setValidator(ContactTable::FILTER_SEARCH, new sfValidatorString(array(
        'required' => false
    )));
  }

  function filter(Doctrine_Query $query) {
    return sfGuardUserTable::getInstance()->filter($query, $this);
  }

}
