<?php

class FilterContactForm extends policatFilterForm {

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
    return ContactTable::getInstance()->filter($query, $this);
  }

}
